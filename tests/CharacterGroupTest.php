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
        $this->structure = new Structure('/form/{t:\d+}/profile');
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
}
