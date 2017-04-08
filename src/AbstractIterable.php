<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:13
 */
namespace samsonframework\stringconditiontree;

use Countable;
use InvalidArgumentException;
use Iterator;

/**
 * Class GenericIterable
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
abstract class AbstractIterable implements Iterator, Countable
{
    /** string Internal collection name for iteration and counting */
    protected const COLLECTION_NAME = 'internalCollection';

    /** @var array Internal internalCollection storage */
    private $internalCollection;

    /**
     * GenericIterable constructor.
     *
     * @param string $collectionName Collection variable nested class name
     *
     * @throws InvalidArgumentException If internalCollection variable does not exists
     */
    public function __construct(string $collectionName = self::COLLECTION_NAME)
    {
        if (!property_exists($this, $collectionName)) {
            throw new InvalidArgumentException(
                'Cannot create iterable - internalCollection [' . $collectionName . '] does not exists'
            );
        }

        // Set pointer for internal iterable and countable collection to passed property by its name
        $this->internalCollection = &$this->$collectionName;

        // Set empty array
        $this->internalCollection = [];
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->internalCollection);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->internalCollection);
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
        return key($this->internalCollection);
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->internalCollection);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->internalCollection);
    }
}
