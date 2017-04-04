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

    /** @var array Expected string condition tree */
    protected $expected = [
        'p' => [
            '@self' => 'p',
            '/' => [
                'test' => [
                    '/' => ['@self'=> 'p/test/'],
                    '-me/' => ['@self' => 'p/test-me/']
                ],
                '{parameter}/' => [
                    '@self' => 'p/{parameter}/',
                    'name' => ['@self' => 'p/{parameter}/name'],
                ],
                '{id:d+}' => ['@self' => 'p/{id:d+}'],
                '{id}' => [// @molodyko - This is very ruff =)
                    '@self' => 'p/{id}',
                    '/' => ['@self' => 'p/{id}/']
                ],
            ]
        ],
        '/' => [
            '@self' => '/',
            't' => [
                'est/' => [
                    'string' => [
                        '@self' => '/test/string',
                        '/inner' => ['@self' => '/test/string/inner']
                    ],
                    '{user_id}/' => ['@self' => '/test/{user_id}/'],
                    'user_id/' => ['@self' => '/test/user_id/']
                ],
                'ube/string' => ['@self' => '/tube/string']
            ],
            'second-test/' => [
                'inner' => ['@self' => '/second-test/inner'],
                'string/inner' => ['@self' => '/second-test/string/inner'],
            ],
        ],
        'test/' => [
            '@self' => 'test/',
            'this-please' => ['@self' => 'test/this-please']
        ],
        '{parameter}' => ['@self' => '{parameter}'],
        '{param}-{parameter}' => [
            '@self' => '{param}-{parameter}',
            '/test' => ['@self' => '{param}-{parameter}/test']
        ],
    ];

    /** @var array Input strings array */
    protected $input = [
        '/test/string' => '#1',
        '/test/string/inner' => '#2',
        '/tube/string' => '#3',
        '/second-test/string/inner' => '#4',
        '/second-test/inner' => '#5',
        '/' => '#6',
        'test/' => '#7',
        'test/this-please' => '#8',
        'p' => '#9',
        'p/test/' => '#10',
        'p/test-me/' => '#11',
        'p/{id}' => '#12',
        'p/{id}/' => '#13',
        'p/{id:d+}' => '#14',
        'p/{parameter}/' => '#15',
        'p/{parameter}/name' => '#16',
        '/test/{user_id}/' => '#17',
        '/test/user_id/' => '#18',
        '{parameter}' => '#19',
        '{param}-{parameter}' => '#20',
        '{param}-{parameter}/test' => '#21',
    ];

    public function setUp()
    {
        $this->sct = new StringConditionTree();
    }

    public function testProcess()
    {
        $nodes = $this->sct->process(array_keys($this->input))->toArray();
        $this->assertEquals($this->expected, $nodes);
    }
}
