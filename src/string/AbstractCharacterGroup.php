<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:28
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes string structure character group.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
abstract class AbstractCharacterGroup
{
    /** @var int Character group length */
    public $length;

    /**
     * AbstractCharacterGroup constructor.
     *
     * @param int $length Character group length
     */
    public function __construct(int $length = 0)
    {
        $this->length = $length;
    }
}
