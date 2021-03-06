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
class TreeNode extends AbstractIterable
{
    /** string Internal collection name for iteration and counting */
    protected const COLLECTION_NAME = 'children';

    /** @var TreeNode[] Collection of tree node children */
    public $children = [];

    /** @var string Tree node identifier */
    public $identifier;

    /** @var self Pointer to parent node */
    public $parent;

    /** @var string Tree node value */
    public $value;

    /** @var string Tree node full value */
    public $fullValue;

    /**
     * TreeNode constructor.
     *
     * @param string   $value  Node value
     * @param string   $identifier Node identifier
     * @param TreeNode $parent Pointer to parent node
     */
    public function __construct(string $value = '', string $identifier = '', TreeNode $parent = null)
    {
        parent::__construct();

        $this->value = $value;
        $this->parent = $parent;
        $this->identifier = $identifier;
        $this->fullValue = $parent !== null ? $parent->fullValue . $value : '';
    }

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
         * @var TreeNode $child
         */
        foreach ($this as $key => $child) {
            $result[$key] = $child->toArray();
        }

        return $result;
    }

    /**
     * Append new node instance and return it.
     *
     * @param string $value Node value
     * @param string $identifier Node identifier
     *
     * @return TreeNode New created node instance
     */
    public function append(string $value, string $identifier): TreeNode
    {
        return $this->children[$value] = new self($value, $identifier, $this);
    }
}
