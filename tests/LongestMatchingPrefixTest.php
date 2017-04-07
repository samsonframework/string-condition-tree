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
    public function testGet()
    {
        $lmp = new CommonPrefix('/test/{p}');

        $this->assertEquals('/te', $lmp->getCommonPrefix('/te{p}'));
    }
}
