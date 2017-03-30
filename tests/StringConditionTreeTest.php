<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: VITALYIEGOROV
 * Date: 12.01.16
 * Time: 14:33
 */
namespace samsonframework\stringconditiontree\tests;

use samsonframework\stringconditiontree\StringConditionTree;

class StringConditionTreeTest extends \PHPUnit_Framework_TestCase
{
    /** @var StringConditionTree */
    protected $sct;

    /** @var array Input strings array */
    protected $input = [
        '/test/string' => '#1',
        '/test/string/inner' => '#2',
        '/tube/string' => '#3',
        '/second-test/string/inner' => '#4',
        '/second-test/inner' => '#5',
        '/' => '#6',
    ];

    /** @var array Expected string condition tree */
    protected $expected = [
        '/' => [],
        '/t' => [
            'est/string' => [
                [],
                '/inner' => []
            ],
            'ube/string' => []
        ],
        '/second-test/' => [
            'string/inner' => [],
            'inner' => []
        ]
    ];

    public function setUp()
    {
        $this->sct = new StringConditionTree();
        sort($this->expected);
    }

    public function testProcess()
    {
        $this->assertEquals($this->expected, $this->sct->process(array_keys($this->input)));
    }
}
