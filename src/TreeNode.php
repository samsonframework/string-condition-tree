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
}
