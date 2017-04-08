<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:46
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\FixedCG;
use samsonframework\stringconditiontree\string\FixedVariableCG;
use samsonframework\stringconditiontree\string\Structure;
use samsonframework\stringconditiontree\string\VariableCG;
use samsonframework\stringconditiontree\string\VariableFixedCG;

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

                FixedVariableCG::class,
                FixedCG::class,
                //VariableFixedCG::class,
                //FixedVariableFixedCG::class
            ],
            '{p}form/{t:\d+}' => [
                VariableFixedCG::class,
                VariableCG::class,
            ],
            '{p}/{p}/form' => [
                VariableFixedCG::class,
                VariableFixedCG::class
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

    public function testGetCommonPrefix()
    {
        $data = [
            'cms/gift/' => ['cms/gift/{id}/{search}', 'cms/gift/form/{id}'],
            '/' => ['/{entity}/{id}/form', '/{id}/test/{page:\d+}'],
            '/test/{p}' => ['/test/{p}', '/test/{p}/'],
            '{p}/test/' => ['{p}/test/{f}', '{p}/test/{z}/'],
            '/te' => ['/test/{p}', '/te{p}'],
            '{p}/' => ['{p}/test', '{p}/form'],
            '{p}' => ['{p}test', '{p}form'],
            'test' => ['test', 'testing'],
            '' => ['{p}', '{parameter}'],
        ];

        foreach ($data as $lmp => $strings) {
            $this->assertEquals($lmp, (new Structure($strings[0]))->getCommonPrefix(new Structure($strings[1])));
            $this->assertEquals($lmp, (new Structure($strings[1]))->getCommonPrefix(new Structure($strings[0])));
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

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
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

        $this->assertEquals(0, $initial->compare($compared));
        $this->assertEquals(0, $compared->compare($initial));
    }

    public function testInitialFirstFixedLongerThanCompared()
    {
        $initial = new Structure('/form/{t:\d+}/profile');
        $compared = new Structure('/form-one/{t:\d+}/profile');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
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

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
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

    public function testEndsWithFixedAfterVariable()
    {
        $initial = new Structure('{param}-{parameter}');
        $compared = new Structure('{p}/{p}/form');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testEndsWithFixedAfterVariable3()
    {
        $initial = new Structure('{p}/{p}/form');
        $compared = new Structure('{z}/');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testEndsWithFixedAfterVariable2()
    {
        $initial = new Structure('{entity}/{id}/form');
        $compared = new Structure('{id}/test/{page:\d+}');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testFirstInnerFixedLongerBetweenVariable()
    {
        $initial = new Structure('{t:\d+}/store/{p}');
        $compared = new Structure('{t:\d+}/store{p}');

        $this->assertEquals(1, $initial->compare($compared));
        $this->assertEquals(-1, $compared->compare($initial));
    }

    public function testVariableFixedVariableToFixed()
    {
        $initial = new Structure('{param}-{parameter}');
        $compared = new Structure('/test/');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

    public function testLongerVariableFilter()
    {
        $initial = new Structure('{param:\d+}');
        $compared = new Structure('{p:[12345]+}');

        $this->assertEquals(-1, $initial->compare($compared));
        $this->assertEquals(1, $compared->compare($initial));
    }

//    public function testVariableFixedWithFixedVariable()
//    {
//        $initial = new Structure('{id}/search');
//        $compared = new Structure('form/{id}');
//
//        $this->assertEquals(-1, $initial->compare($compared));
//        $this->assertEquals(1, $compared->compare($initial));
//    }
}
