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
class TreeNode extends IterableTreeNode
{
    /** @var self Pointer to parent node */
    public $parent;

    /** @var string Tree node value */
    public $value;

    /** @var string Tree node identifier */
    public $identifier;

    /** @var string Tree node full value */
    public $fullValue;

    /**
     * TreeNode constructor.
     *
     * @param string   $value  Node value
     * @param string   $identifier Node identifier
     * @param TreeNode $parent Pointer to parent node
     */
    public function __construct(string $value = '', string $identifier = '', self $parent = null)
    {
        $this->value = $value;
        $this->parent = $parent;
        $this->identifier = $identifier;

        if ($parent !== null) {
            $this->fullValue = $parent->fullValue . ($value !== StringConditionTree::SELF_NAME ? $value : '');
        }
    }

    /**
     * Append new node instance and return it.
     *
     * @param string $value Node value
     * @param string $identifier Node identifier
     *
     * @return TreeNode New created node instance
     */
    public function append(string $value, string $identifier): self
    {
        return $this->children[$value] = new self($value, $identifier, $this);
    }

    /**
     * Convert tree node to associative array.
     *
     * @return array Tree structure as hashed array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this as $key => $child) {
            if ($key !== StringConditionTree::SELF_NAME) {
                $result[$key] = $child->toArray();
            } else { // Store full node value
                $result[$key] = $this->identifier;
            }
        }

        return $result;
    }


}
