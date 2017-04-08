<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:24
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;

/*
 * Class AbstractIterableTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class AbstractIterableTest extends TestCase
{
    public function testAbstractIterable()
    {
        $testIterable = new TestAbstractIterable();
        $testIterable->collection = [1,'2',3,4,5,6,7,8,9,0];

        foreach ($testIterable as $index => $item) {
            $this->assertEquals($testIterable->collection[$index], $item);
        }
    }
}
