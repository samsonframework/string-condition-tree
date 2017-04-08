<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\AbstractCG;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\VariableCG;

/**
 * Class CharacterGroupTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class AbstractCGTest extends TestCase
{
    /** @var AbstractCG[] */
    protected $groups = [];

    public function setUp()
    {
        $this->groups[] = new FixedCG('', 6);
        $this->groups[] = new VariableCG('', 7);
        $this->groups[] = new FixedCG('', 9);
        $this->groups[] = new VariableCG('', 3);
        $this->groups[] = new FixedCG('', 6);
    }

    public function testGetString()
    {
        $this->assertEquals('test', (new FixedCG('test'))->getString());
    }

    public function testSameType()
    {
        $this->assertTrue($this->groups[0]->isSameType($this->groups[2]));
        $this->assertFalse($this->groups[0]->isSameType($this->groups[1]));
        $this->assertFalse($this->groups[1]->isSameType($this->groups[2]));
    }
}
