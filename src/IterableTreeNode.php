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
     * Return the current element
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * Move forward to next element
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * Return the key of the current element
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * Checks if current position is valid
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        $key = key($this->children);

        return ($key !== null && $key !== false);
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->children);
    }
}
