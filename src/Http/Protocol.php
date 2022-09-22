<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http;

use Plattry\Event\Network\ConnectionInterface;
use Plattry\Event\Network\ProtocolInterface;
use Plattry\Kit\Http\Foundation\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A http protocol instance.
 */
class Protocol implements ProtocolInterface
{
    /**
     * The http factory instance.
     * @var HttpFactory
     */
    protected HttpFactory $factory;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->factory = new HttpFactory();
    }

    /**
     * Detach request data bag from raw.
     * @param string $raw
     * @return array
     */
    protected static function detachRequestBag(string $raw): array
    {
        // Get the raw header and body.
        $hbSplitPos = strpos($raw, "\r\n\r\n");
        $rawHeader = substr($raw, 0, $hbSplitPos);
        $rawBody = substr($raw, $hbSplitPos + 4);

        // Get the raw first line and other lines.
        $foSplitPos = strpos($raw, "\r\n");
        $firstLine = substr($rawHeader, 0, $foSplitPos);
        $otherLine = substr($rawHeader, $foSplitPos + 2);

        // Get the method, target and protocol version from first line.
        $firstLineArr = explode(' ', $firstLine, 3);
        $method = $firstLineArr[0];
        $target = $firstLineArr[1] ?? '/';
        $version = substr($firstLineArr[2] ?? '', 5) ?: '1.0';

        // Get header data from the order lines.
        $headers = [];
        foreach (explode("\r\n", $otherLine) as $line) {
            [$key, $value] = explode(':', $line, 2);
            $headers[strtolower(trim($key))] = trim($value);
        }

        // Return data if body is empty.
        if (empty($rawBody)) {
            return [$method, $target, $version, $headers, $rawBody, [], []];
        }

        // Get parsed body data and uploaded files from raw body.
        [$parsedBody, $files] = static::parseBody($headers['content-type'] ?? '', $rawBody);

        return [$method, $target, $version, $headers, $rawBody, $parsedBody, $files];
    }

    /**
     * Parse body by content-type.
     * @param string $contentType
     * @param string $rawBody
     * @return array[]
     */
    protected static function parseBody(string $contentType, string $rawBody): array
    {
        $parsedBody = $files = [];
        if (str_contains($contentType, 'json')) {
            $parsedBody = (array)json_decode($rawBody, true);
        } elseif (str_contains($contentType, 'form-data')) {
            $boundary = '--' . strstr($contentType, '--') . "\r\n";
            $rawBody = substr($rawBody, 0, -strlen($boundary) - 2);
            foreach (explode($boundary, $rawBody) as $rawBoundary) {
                if (empty($rawBoundary)) continue;

                [$boundary_header, $boundary_value] = explode("\r\n\r\n", $rawBoundary, 2);
                $boundary_header = strtolower($boundary_header);
                $boundary_value = substr($boundary_value, 0, -2);

                preg_match('/name="(.*?)"/', $boundary_header, $name);
                if (empty($name)) continue;

                preg_match('/filename="(.*?)"/', $boundary_header, $filename);
                preg_match('/content-type: (.+)?/', $boundary_header, $type);

                // Is not a file.
                if (empty($filename) || empty($type)) {
                    $parsedBody[$name[1]] = $boundary_value;
                    continue;
                }

                // Is a file.
                $error = UPLOAD_ERR_OK;
                $tmp_name = tempnam(sys_get_temp_dir(), 'rush_upload_');
                if (false === $tmp_name || false === file_put_contents($tmp_name, $boundary_value)) {
                    $error = UPLOAD_ERR_CANT_WRITE;
                }

                $files[$name[1]] = [
                    'tmp_name' => $tmp_name,
                    'name' => $filename[1],
                    'size' => strlen($boundary_value),
                    'type' => $type[1],
                    'error' => $error
                ];
            }
        } else {
            parse_str($rawBody, $parsedBody);
        }

        return [$parsedBody, $files];
    }

    /**
     * Compile response data bag to raw.
     * @param string $version
     * @param int $code
     * @param string $phrase
     * @param array $headers
     * @param string $body
     * @return string
     */
    protected static function compileResponseBag(string $version, int $code, string $phrase, array $headers, string $body): string
    {
        // Compile first line.
        $raw = sprintf("HTTP/%s %d %s\r\n", $version, $code, $phrase);

        // Compile header.
        $headers["content-length"] = [strlen($body)];

        $cookies = $headers["set-cookie"] ?? [];
        unset($headers["set-cookie"]);

        ksort($headers);
        foreach ($headers as $name => $header) {
            $raw .= sprintf("%s: %s\r\n", $name, implode(";", $header));
        }

        foreach ($cookies as $cookie) {
            $raw .= sprintf("set-cookie: %s\r\n", $cookie);
        }

        // Compile body.
        $raw .= sprintf("\r\n%s", $body);

        return $raw;
    }

    /**
     * @inheritDoc
     */
    public function check(ConnectionInterface $connection, string $input): int
    {
        $crlf = strpos($input, "\r\n\r\n");
        if (false === $crlf) {
            if (strlen($input) >= 16384) {
                $connection->close("HTTP/1.1 413 Request Entity Too Large\r\n\r\n", true);
            }

            return 0;
        }

        $method = strstr($input, ' ', true);

        if (
            'GET' === $method || 'HEAD' === $method ||
            'DELETE' === $method || 'OPTIONS' === $method || 'TRACE' === $method
        ) {
            return $crlf + 4;
        }

        if ('POST' !== $method && 'PUT' !== $method && 'PATCH' !== $method) {
            $connection->close("HTTP/1.1 400 Bad Request\r\n\r\n", true);
            return 0;
        }

        $header = substr($input, 0, $crlf);
        preg_match("/\r\ncontent-length: ?(\d+)/i", $header, $match);
        if (!isset($match[1]) || !is_numeric($match[1])) {
            $connection->close("HTTP/1.1 400 Bad Request\r\n\r\n", true);
            return 0;
        }

        return $crlf + 4 + (int)$match[1];
    }

    /**
     * @inheritDoc
     * @return ServerRequestInterface
     */
    public function decode(ConnectionInterface $connection, string $raw): mixed
    {
        [$method, $target, $version, $headers, $rawBody, $parsedBody, $files] = self::detachRequestBag($raw);

        $request = $this->factory->createServerRequest($method, $target, $connection->getAttribute())
            ->withProtocolVersion($version)->withHeaders($headers)->withParsedBody($parsedBody);

        $request->getBody()->write($rawBody);
        $request->getBody()->rewind();

        parse_str(str_replace('; ', '&', $headers['cookie'] ?? ''), $cookies);
        $request->withCookieParams($cookies);

        parse_str((string)parse_url($target, PHP_URL_QUERY), $query);
        $request->withQueryParams($query);

        array_walk($files, function (&$val) {
            $stream = $this->factory->createStreamFromFile($val['tmp_name']);
            $val = $this->factory->createUploadedFile(
                $stream, $val['size'], $val['error'], $val['name'], $val['type']
            );
        });
        $request->withUploadedFiles($files);

        return $request;
    }

    /**
     * @inheritDoc
     * @param ResponseInterface $data
     */
    public function encode(ConnectionInterface $connection, mixed $data): string
    {
        $version = $data->getProtocolVersion();
        $code = $data->getStatusCode();
        $phrase = $data->getReasonPhrase();
        $headers = $data->getHeaders();
        $body = strval($data->getBody());

        return self::compileResponseBag($version, $code, $phrase, $headers, $body);
    }
}
