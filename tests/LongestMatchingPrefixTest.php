<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 07.04.17 at 08:37
 */
namespace samsonframework\stringconditiontree\tests;

use PHPUnit\Framework\TestCase;
use samsonframework\stringconditiontree\string\CommonPrefix;

/**
 * Class LongestMatchingPrefixTest
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class LongestMatchingPrefixTest extends TestCase
{
    public function testGetCommonPrefix()
    {
        $data = [
            '/te' => ['/test/{p}', '/te{p}'],
            '/test/{p}' => ['/test/{p}', '/test/{p}/'],
        ];

        foreach ($data as $lmp => $strings) {
            $this->assertEquals($lmp, (new CommonPrefix($strings[0]))->getCommonPrefix($strings[1]));
            $this->assertEquals($lmp, (new CommonPrefix($strings[1]))->getCommonPrefix($strings[0]));
        }
    }
}
