<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:26
 */
namespace samsonframework\stringconditiontree\tests;

use samsonframework\stringconditiontree\AbstractIterable;

/**
 * Class TestAbastractIterable
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class TestAbstractIterable extends AbstractIterable
{
    /** @var array Test array */
    public $collection = [];

    /**
     * TestAbastractIterable constructor.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct('collection');
    }
}
