<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 05.04.17 at 11:43
 */
namespace samsonframework\stringconditiontree;

/**
 * Class IterableTreeNode
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class IterableTreeNode implements \Iterator
{
    /** @var IterableTreeNode[] Collection of tree node children */
    public $children = [];

    /** @var string Tree node identifier */
    public $identifier;

    /**
     * Convert tree node to associative array.
     *
     * @return array Tree structure as hashed array
     */
    public function toArray(): array
    {
        $result = [];

        // Render @self item for tests
        if ($this->identifier !== '') {
            $result[StringConditionTree::SELF_NAME] = $this->identifier;
        }

        /**
         * @var string $key
         * @var IterableTreeNode $child
         */
        foreach ($this as $key => $child) {
            $result[$key] = $child->toArray();
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $key = key($this->children);

        return ($key !== null && $key !== false);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->children);
    }
}
