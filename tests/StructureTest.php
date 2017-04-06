<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCharacterGroup;
use samsonframework\stringconditiontree\string\Structure;
use samsonframework\stringconditiontree\string\VariableCharacterGroup;

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
                VariableCharacterGroup::class,
            ],
            'p' => [
                FixedCharacterGroup::class,
            ],
            '/form/{t:\d+}/profile' => [
                FixedCharacterGroup::class,
                VariableCharacterGroup::class,
                FixedCharacterGroup::class
            ],
            '{p}form/{t:\d+}' => [
                VariableCharacterGroup::class,
                FixedCharacterGroup::class,
                VariableCharacterGroup::class,
            ],
        ];

        /** @var array $structure */
        foreach ($creationData as $string => $structure) {
            $input = new Structure($string);
            foreach ($structure as $index => $characterGroupClass) {
                $this->assertInstanceOf($characterGroupClass, $input->groups[$index]);
            }
        }
    }

    public function testInitialFixedLongerThanCompared()
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

    public function testInitialFirstVariableEqualToComparedWithParameterSecondFixedLonger()
    {
        $initial = new Structure('{t:\d+}/store/{p}');
        $compared = new Structure('{t:\d+}/profile');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testInitialFirstFixedEqualToComparedRestEqual()
    {
        $initial = new Structure('/form-one/{t:\d+}/profile');
        $compared = new Structure('/form-two/{t:\d+}/profile');

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }
}
