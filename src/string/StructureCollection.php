<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:08
 */
namespace samsonframework\stringconditiontree\string;

use Iterator;

/**
 * Class StructureCollection
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StructureCollection implements Iterator, Countable
{
    /** @var Structure[] */
    protected $structures = [];

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->structures);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->structures);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $key = $this->key();

        return ($key !== null && $key !== false);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->structures);
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->structures);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->structures);
    }
}
