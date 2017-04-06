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
            '@self' => '#9',
            '/' => [
                'test' => [
                    '/' => ['@self'=> '#10'],
                    '-me/' => ['@self' => '#11']
                ],
                '{parameter}/' => [
                    '@self' => '#15',
                    'name' => ['@self' => '#16'],
                ],
                '{id:d+}' => ['@self' => '#14'],
                '{id}' => [
                    '@self' => '#12',
                    '/' => ['@self' => '#13']
                ],
            ]
        ],
        '/' => [
            '@self' => '#6',
            't' => [
                'est/' => [
                    'string' => [
                        '@self' => '#1',
                        '/inner' => ['@self' => '#2']
                    ],
                    'user_id/' => ['@self' => '#18'],
                    '{user_id}/' => ['@self' => '#17'],
                ],
                'ube/string' => ['@self' => '#3']
            ],
            'cms/gift/' => [
                'form/{id}' => ['@self' => '#22'],
                '{id}/{search}' => ['@self' => '#23'],
            ],
            'second-test/' => [
                'inner' => ['@self' => '#5'],
                'string/inner' => ['@self' => '#4'],
            ],
            '{id}/test/{page:\d+}' => ['@self' => '#29'],
            '{entity}/{id}/form' => ['@self' => '#28'],
            '{num}/{page:\d+}' => ['@self' => '#30'],
        ],
        'test/' => [
            '@self' => '#7',
            'this-please' => ['@self' => '#8']
        ],
        '{z}/te' => [
            '/{y:0\d+}' => ['@self' => '#26'],
            'st/{y:01\d+}' => ['@self' => '#27'],
        ],
        '{z}/' => [
            '{p}/{y:\d+}' => ['@self' => '#25'],
        ],
        '{p}/{p}/form' => ['@self' => '#24'],
        '{param}-{parameter}' => [
            '@self' => '#20',
            '/test' => ['@self' => '#21']
        ],
        '{parameter}' => ['@self' => '#19'],
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
        '/cms/gift/form/{id}' => '#22',
        '/cms/gift/{id}/{search}' => '#23',
        '{p}/{p}/form' => '#24',
        '{z}/{p}/{y:\d+}' => '#25',
        '{z}/te/{y:0\d+}' => '#26',
        '{z}/test/{y:01\d+}' => '#27',
        '/{entity}/{id}/form' => '#28',
        '/{id}/test/{page:\d+}' => '#29',
        '/{num}/{page:\d+}' => '#30',
    ];

    public function setUp()
    {
        $this->sct = new StringConditionTree();
    }

    public function testProcess()
    {
        $nodes = $this->sct->process($this->input)->toArray();

        //$this->assertSame($this->expected, $nodes);
    }
}
