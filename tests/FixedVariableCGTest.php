<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 12:47
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableCG;
use samsonframework\stringconditiontree\string\NullCG;
use samsonframework\stringconditiontree\string\VariableCG;
use samsonframework\stringconditiontree\string\VariableFixedCG;

/**
 * Class FixedVariableCGTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FixedVariableCGTest extends TestCase
{
    public function testFromString()
    {
        $input = '/test/{z}';
        $this->assertInstanceOf(FixedVariableCG::class, FixedVariableCG::fromString($input));
        $this->assertEquals('', $input);
    }

    public function testWrongFromString()
    {
        $input = '{z}/test/{z}';
        $this->assertEquals(null, FixedVariableCG::fromString($input));
    }

    public function testCompareWithSameSizeFixedCG()
    {
        $initial = new FixedVariableCG('test/{p}');
        $compared = new FixedVariableCG('test/{z}');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testCompareWithSameSizeFixedCGWithFilteredVariableCG()
    {
        $initial = new FixedVariableCG('test/{p:filter}');
        $compared = new FixedVariableCG('test/{z}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithSameSizeFixedCGWithFilteredVariableCG2()
    {
        $initial = new FixedVariableCG('/cms/gist/{id}/{search}');
        $compared = new FixedVariableCG('/cms/gift/from/{id}');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testCompareWithShorterSizeFixedCG()
    {
        $initial = new FixedVariableCG('test/{p}');
        $compared = new FixedVariableCG('t/{z}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithShorterSizeFixedCGAndFilteredVariableCG()
    {
        $initial = new FixedVariableCG('test/{p:filter}');
        $compared = new FixedVariableCG('t/{z}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testCompareWithFixedCG()
    {
        $initial = new FixedVariableCG('test/{p}');
        $compared = new FixedCG('t/');

        $this->assertEquals(-1, $initial->compare($compared));
    }

    public function testCompareWithVariableCG()
    {
        $initial = new FixedVariableCG('test/{p}');
        $compared = new VariableCG('{p}');

        $this->assertEquals(1, $initial->compare($compared));
    }

    public function testCompareWithFilteredVariableCG()
    {
        $initial = new FixedVariableCG('test/{p}');
        $compared = new VariableCG('{p:filter}');

        $this->assertEquals(1, $initial->compare($compared));
    }

    public function testCompareWithVariableFixedCG()
    {
        $initial = new FixedVariableCG('te/{a}');
        $compared = new VariableFixedCG('{p}/te');

        $this->assertEquals(1, $initial->compare($compared));
    }
}
