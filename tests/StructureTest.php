<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableFixedCG;
use samsonframework\stringconditiontree\string\Structure;
use samsonframework\stringconditiontree\string\VariableCG;

/**
 * Class StructureTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StructureTest extends TestCase
{
    public function testConstructor()
    {
        /** @var array Data for testing character group structure creation */
        $creationData = [
            '{p}' => [
                VariableCG::class,
            ],
            'p' => [
                FixedCG::class,
            ],
            '/form/{t:\d+}/profile' => [
                FixedVariableFixedCG::class
            ],
            '{p}form/{t:\d+}' => [
                VariableCG::class,
                FixedCG::class,
                VariableCG::class,
            ],
            '{p}/{p}/form' => [
                VariableCG::class,
                FixedVariableFixedCG::class
            ]
        ];

        /** @var array $structure */
        foreach ($creationData as $string => $structure) {
            $input = new Structure($string);
            foreach ($structure as $index => $characterGroupClass) {
                $this->assertArrayHasKey(
                    $index,
                    $input->groups,
                    'Wrong character group structure building for: ' . $string
                );
                $this->assertInstanceOf($characterGroupClass, $input->groups[$index]);
            }
        }
    }

    public function testVariableFixedToVariableFixedVariableWithEqualFixed()
    {
        $initial = new Structure('{p}/test/');
        $compared = new Structure('{p}/test/{p1}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testFixedVariableFixedToVariableFixedVariableWithEqualSecondFixed()
    {
        $initial = new Structure('string/{p}/test/');
        $compared = new Structure('low/{p}/test/{p1}');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testFixedLongerThanCompared()
    {
        $initial = new Structure('/form-one/');
        $compared = new Structure('/form');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testInitialVariableLongerThanCompared()
    {
        $initial = new Structure('{param}');
        $compared = new Structure('{p}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testInitialFirstFixedLongerThanCompared()
    {
        $initial = new Structure('/form/{t:\d+}/profile');
        $compared = new Structure('/form-one/{t:\d+}/profile');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testInitialFirstFixedEqualToComparedSecondVariableLonger()
    {
        $initial = new Structure('/form-one/{t:\d+}/profile');
        $compared = new Structure('/form-two/{t}/profile');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testInitialFirstVariableEqualToComparedSecondFixedLonger()
    {
        $initial = new Structure('{t:\d+}/store');
        $compared = new Structure('{t:\d+}/profile');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testInitialFirstVariableEqualToComparedWithParameterSecondFixedEqual()
    {
        $initial = new Structure('{t:\d+}/profile/{p}');
        $compared = new Structure('{t:\d+}/profile/');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testInitialFirstVariableEqualWithVariableToComparedWithShorterSecondShorter()
    {
        $initial = new Structure('{t:\d+}/store/{p}');
        $compared = new Structure('{t:\d+}/store');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testInitialFirstFixedEqualToComparedRestEqual()
    {
        $initial = new Structure('/form-one/{t:\d+}/profile');
        $compared = new Structure('/form-two/{t:\d+}/profile');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testEndsWithFixedAfterVariable()
    {
        $initial = new Structure('{param}-{parameter}');
        $compared = new Structure('{p}/{p}/form');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

//    public function testFirstInnerFixedLongerBetweenVariable()
//    {
//        $initial = new Structure('{t:\d+}/store/{p}');
//        $compared = new Structure('{t:\d+}/store{p}');
//
//        $this->assertEquals(1, $initial->compare($compared));
//        $this->assertEquals(-1, $compared->compare($initial));
//    }
}
