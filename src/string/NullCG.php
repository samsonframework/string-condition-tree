<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 07.04.17 at 07:43
 */
namespace samsonframework\stringconditiontree\string;

/**
 * Null character group with lowest priority.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class NullCG extends AbstractCG
{
    /**
     * NullCG constructor.
     */
    public function __construct()
    {
        parent::__construct('', 0);
    }

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCG $group): int
    {
        // Passed character group always has higher priority
        return -1;
    }
}
