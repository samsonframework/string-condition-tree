<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 31.03.17 at 09:23
 */
namespace samsonframework\stringconditiontree;

/**
 * Class TreeNode
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class TreeNode implements \Iterator
{
    /** @var mixed Tree node value */
    public $value;

    /** @var self[] Collection of tree node children */
    public $children = [];

    /**
     * TreeNode constructor.
     *
     * @param string $value Node value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Append new node instance and return it.
     *
     * @param string $value Node value
     *
     * @return self New created node instance
     */
    public function append(string $value): self
    {
        return $this->children[$value] = new self($value);
    }

    /**
     * @return array Tree structure as hashed array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->children as $child) {
            $result[$child->value] = $child->toArray();
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
        return next($this->children);
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
