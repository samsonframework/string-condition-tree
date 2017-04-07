<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 07.04.17 at 09:08
 */
namespace samsonframework\stringconditiontree\string;

use Countable;
use Iterator;

/**
 * Class IterableStructure
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class IterableStructure implements Iterator, Countable
{
    /** @var AbstractCG[] */
    public $groups = [];

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->groups);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->groups);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->groups);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $key = key($this->groups);

        return ($key !== null && $key !== false);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->groups);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->groups);
    }
}
