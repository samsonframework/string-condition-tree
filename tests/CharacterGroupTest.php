<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\Structure;

/**
 * Class CharacterGroupTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class CharacterGroupTest extends TestCase
{
    /** @var Structure */
    protected $structure;

    public function setUp()
    {
        $this->structure = new Structure('/form/{t:\d+}/profile/{s}/test/');
    }

    public function testSameType()
    {
        $this->assertTrue($this->structure->groups[0]->isSameType($this->structure->groups[2]));
        $this->assertFalse($this->structure->groups[0]->isSameType($this->structure->groups[1]));
        $this->assertFalse($this->structure->groups[1]->isSameType($this->structure->groups[2]));
    }

    public function testIsFixed()
    {
        $this->assertTrue($this->structure->groups[0]->isFixed());
        $this->assertFalse($this->structure->groups[1]->isFixed());
        $this->assertTrue($this->structure->groups[2]->isFixed());
    }

    public function testIsVariable()
    {
        $this->assertFalse($this->structure->groups[0]->isVariable());
        $this->assertTrue($this->structure->groups[1]->isVariable());
        $this->assertFalse($this->structure->groups[2]->isVariable());
    }

    public function testCompare()
    {
        // Fixed higher than variable
        $this->assertEquals(
            1,
            $this->structure->groups[0]->compare($this->structure->groups[1])
        );

        // Variable lower than fixed
        $this->assertEquals(
            -1,
            $this->structure->groups[1]->compare($this->structure->groups[0])
        );

        // 2nd fixed longer then 1st fixed
        $this->assertEquals(
            -1,
            $this->structure->groups[0]->compare($this->structure->groups[2])
        );

        // 1st fixed equal length to 4th fixed
        $this->assertEquals(
            0,
            $this->structure->groups[4]->compare($this->structure->groups[0])
        );

        // 4st fixed equal length to 1th fixed
        $this->assertEquals(
            0,
            $this->structure->groups[0]->compare($this->structure->groups[4])
        );
    }
}
