<?php

declare(strict_types = 1);

namespace Plattry\Kit\Route;

use Closure;

/**
 * A router instance.
 */
class Router implements RouterInterface
{
    /**
     * The rule-tree instance.
     * @var RuleTree
     */
    public RuleTree $root;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->root = new RuleTree();
    }

    /**
     * @inheritDoc
     */
    public function register(string $path, array $middlewares, string|Closure $target): void
    {
        $rule = new Rule($path, $middlewares, $target);

        $idxStr = preg_replace("#:[^/.]+#", RuleTree::WILDCARD, $rule->getPath());
        $idxArr = explode("/", $idxStr);

        $this->root->addNode($idxArr, $rule);
    }

    /**
     * @inheritDoc
     */
    public function parse(string $path, array &$query = null): RuleInterface|null
    {
        $pathArr = explode("/", $path);
        $query = [];

        $rule = $this->root->getNode($pathArr);
        if ($rule === null)
            return null;

        foreach (explode("/", $rule->getPath()) as $i => $index) {
            $index !== "" && $index[0] === ":" && $query[substr($index, 1)] = $pathArr[$i];
        }

        return $rule;
    }
}
