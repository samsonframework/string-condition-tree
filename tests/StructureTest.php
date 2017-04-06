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
    /** @var array Data for testing character group structure creation */
    protected $creationData = [
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

    public function testConstructor()
    {
        /** @var array $structure */
        foreach ($this->creationData as $string => $structure) {
            $input = new Structure($string);
            foreach ($structure as $index => $characterGroupClass) {
                $this->assertInstanceOf($characterGroupClass, $input->groups[$index]);
            }
        }
    }
}
