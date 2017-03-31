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
class TreeNode
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
}
