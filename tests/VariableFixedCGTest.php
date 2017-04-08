<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 12:59
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableCG;
use samsonframework\stringconditiontree\string\NullCG;
use samsonframework\stringconditiontree\string\VariableCG;
use samsonframework\stringconditiontree\string\VariableFixedCG;

/**
 * Class VariableFixedCGTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class VariableFixedCGTest extends TestCase
{
    public function testFromString()
    {
        $input = '{z}/test';
        $this->assertInstanceOf(VariableFixedCG::class, VariableFixedCG::fromString($input));
        $this->assertEquals('', $input);
    }

    public function testWrongFromString()
    {
        $input = '/test/{z}';
        $this->assertInstanceOf(NullCG::class, VariableFixedCG::fromString($input));
    }

    public function testCompareWithSameSizeFixedCG()
    {
        $initial = new VariableFixedCG('{p}/test');
        $compared = new VariableFixedCG('{z}/home');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testCompareWithSameSizeFixedCGAndFilteredVariableCG()
    {
        $initial = new VariableFixedCG('{p:filter}/test');
        $compared = new VariableFixedCG('{z}/home');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithDifferentSizeFixedCG()
    {
        $initial = new VariableFixedCG('{p}/te');
        $compared = new VariableFixedCG('{z}/home');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testCompareWithFixedCG()
    {
        $initial = new VariableFixedCG('{p}/te');
        $compared = new FixedCG('home');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareWithVariableCG()
    {
        $initial = new VariableFixedCG('{p}/te');
        $compared = new VariableCG('{a}');

        $this->assertEquals(1, $initial->compare($compared));
    }

    public function testCompareWithFixedVariableCG()
    {
        $initial = new VariableFixedCG('{p}/te');
        $compared = new FixedVariableCG('te/{a}');

        $this->assertEquals(-1, $initial->compare($compared));
    }
}
