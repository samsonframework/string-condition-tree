<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:36
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\Structure;
use samsonframework\stringconditiontree\string\StructureCollection;

/**
 * Class StructureCollectionTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StructureCollectionTest extends TestCase
{
    public function testFromStringsArray()
    {
        $strings = ['test', 'test2'];
        $structureCollection = StructureCollection::fromStringsArray($strings);

        $this->assertInstanceOf(StructureCollection::class, $structureCollection);

        foreach ($structureCollection as $key => $structure) {
            $this->assertTrue(in_array($key, $strings, true));
            $this->assertInstanceOf(Structure::class, $structure);
        }
    }
}
