<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:34
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes character group with variable length.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class VariableCG extends AbstractCharacterGroup
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'variable';

    /** string Regular expression matching character group */
    const PATTERN_REGEXP = '{.*?}';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.self::PATTERN_REGEXP.')';


    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCharacterGroup $group): int
    {
        /**
         * Longer variable character group has higher priority
         */
        return $this->length <=> $group->length;
    }
}
