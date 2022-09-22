<?php

declare(strict_types=1);

namespace Plattry\Kit\Route;

/**
 * A rule tree instance.
 */
class RuleTree
{
    /**
     * The path wildcard.
     */
    public const WILDCARD = "*";

    /**
     * The child nodes.
     * @var RuleTree[]
     */
    protected array $nodes = [];

    /**
     * The node value.
     * @var RuleInterface|null
     */
    protected RuleInterface|null $rule = null;

    /**
     * Add a node to tree by the index.
     * @param array $index
     * @param RuleInterface $rule
     * @return void
     */
    public function addNode(array $index, RuleInterface $rule): void
    {
        if (empty($index)) {
            $this->rule = $rule;
            return;
        }

        $curr = array_shift($index);

        !isset($this->nodes[$curr]) && $this->nodes[$curr] = new static();

        $this->nodes[$curr]->addNode($index, $rule);
    }

    /**
     * Get a node by the index.
     * @param array $index
     * @return RuleInterface|null
     */
    public function getNode(array $index): RuleInterface|null
    {
        if (empty($index))
            return $this->rule;

        $curr = array_shift($index);

        if (isset($this->nodes[$curr]))
            return $this->nodes[$curr]->getNode($index);

        if (isset($this->nodes[static::WILDCARD]))
            return $this->nodes[static::WILDCARD]->getNode($index);

        return null;
    }
}
