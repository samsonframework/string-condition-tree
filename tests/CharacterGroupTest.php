<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\AbstractCharacterGroup;
use samsonframework\stringconditiontree\string\FixedCharacterGroup;
use samsonframework\stringconditiontree\string\VariableCharacterGroup;

/**
 * Class CharacterGroupTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class CharacterGroupTest extends TestCase
{
    /** @var AbstractCharacterGroup[] */
    protected $groups = [];

    public function setUp()
    {
        $this->groups[] = new FixedCharacterGroup('',6);
        $this->groups[] = new VariableCharacterGroup('',7);
        $this->groups[] = new FixedCharacterGroup('',9);
        $this->groups[] = new VariableCharacterGroup('',3);
        $this->groups[] = new FixedCharacterGroup('',6);
    }

    public function testSameType()
    {
        $this->assertTrue($this->groups[0]->isSameType($this->groups[2]));
        $this->assertFalse($this->groups[0]->isSameType($this->groups[1]));
        $this->assertFalse($this->groups[1]->isSameType($this->groups[2]));
    }

    public function testIsFixed()
    {
        $this->assertTrue($this->groups[0]->isFixed());
        $this->assertFalse($this->groups[1]->isFixed());
        $this->assertTrue($this->groups[2]->isFixed());
    }

    public function testIsVariable()
    {
        $this->assertFalse($this->groups[0]->isVariable());
        $this->assertTrue($this->groups[1]->isVariable());
        $this->assertFalse($this->groups[2]->isVariable());
    }

    public function testCompare()
    {
        // Fixed higher than variable
        $this->assertEquals(
            1,
            $this->groups[0]->compare($this->groups[1])
        );

        // Variable lower than fixed
        $this->assertEquals(
            -1,
            $this->groups[1]->compare($this->groups[0])
        );

        // 2nd fixed longer then 1st fixed
        $this->assertEquals(
            1,
            $this->groups[0]->compare($this->groups[2])
        );

        // 1st fixed shorter then 2nd fixed
        $this->assertEquals(
            -1,
            $this->groups[2]->compare($this->groups[0])
        );

        // 1st fixed equal length to 4th fixed
        $this->assertEquals(
            0,
            $this->groups[4]->compare($this->groups[0])
        );

        // 4st fixed equal length to 1th fixed
        $this->assertEquals(
            0,
            $this->groups[0]->compare($this->groups[4])
        );
    }
}
