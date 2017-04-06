<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:34
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes character group with fixed length.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FixedVariableFixedCG extends AbstractCharacterGroup
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'fixedVariableFixed';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.FixedCG::PATTERN_REGEXP.VariableCG::PATTERN_REGEXP.FixedCG::PATTERN_REGEXP.')';

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCharacterGroup $group): int
    {
        /**
         * Shorter fixed character group has higher priority
         */
        return $group->length <=> $this->length;
    }
}
