<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 12:39
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableCG;
use samsonframework\stringconditiontree\string\NullCG;
use samsonframework\stringconditiontree\string\VariableCG;
use samsonframework\stringconditiontree\string\VariableFixedCG;

/**
 * Class VariableCGTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class VariableCGTest extends TestCase
{
    public function testFromString()
    {
        $input = '{z}/test';
        $this->assertInstanceOf(VariableCG::class, VariableCG::fromString($input));
        $this->assertEquals('/test', $input);
    }

    public function testWrongFromString()
    {
        $input = '/test/{z}';
        $this->assertInstanceOf(NullCG::class, VariableCG::fromString($input));
    }

    public function testCompareWithSameSizeVariableCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new VariableCG('{z}');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testCompareWithDifferentSizeVariableCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new VariableCG('{param}');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testCompareWithFilteredVariableCG()
    {
        $initial = new VariableCG('{p:filter}');
        $compared = new VariableCG('{param}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithBothFilteredVariableCG()
    {
        $initial = new VariableCG('{p:filter}');
        $compared = new VariableCG('{param:\d+}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithFixedCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new FixedCG('param');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareWithFixedVariableCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new FixedVariableCG('param{x}');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareWithVariableFixedCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new VariableFixedCG('{x}/param');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareFilteredWithVariableFixedCG()
    {
        $initial = new VariableCG('{p:filter}');
        $compared = new VariableFixedCG('{x}/param');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareWithFilteredVariableFixedCG()
    {
        $initial = new VariableCG('{p}');
        $compared = new VariableFixedCG('{x:filter}/param');

        $this->assertEquals(-1, $initial->compare($compared));
    }
}
