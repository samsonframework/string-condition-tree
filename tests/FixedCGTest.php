<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 12:32
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableCG;
use samsonframework\stringconditiontree\string\VariableCG;
use samsonframework\stringconditiontree\string\VariableFixedCG;

/**
 * Class FixedCGTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FixedCGTest extends TestCase
{
    public function testCompareWithDifferentSizeFixedCG()
    {
        $initial = new FixedCG('test');
        $compared = new FixedCG('te');

        // Fixed higher than variable
        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testCompareWithSameSizeFixedCG()
    {
        $initial = new FixedCG('test');
        $compared = new FixedCG('perd');

        // Fixed higher than variable
        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testCompareWithVariableCG()
    {
        $initial = new FixedCG('test');
        $compared = new VariableCG('{t}');

        // Fixed higher than variable
        $this->assertEquals(1, $initial->compare($compared));
    }

    public function testCompareWithFixedVariableCG()
    {
        $initial = new FixedCG('test');
        $compared = new FixedVariableCG('p{t}');

        // Fixed higher than variable
        $this->assertEquals(1, $initial->compare($compared));
    }

    public function testCompareWithVariableFixedCG()
    {
        $initial = new FixedCG('test');
        $compared = new VariableFixedCG('{t}p');

        // Fixed higher than variable
        $this->assertEquals(1, $initial->compare($compared));
    }
}
