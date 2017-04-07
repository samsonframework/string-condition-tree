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
class VariableCG extends AbstractCG
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
    protected function compareLength(AbstractCG $group): int
    {
        /** @var VariableCG $group */
        $variableFiltered = $this->variableHasFilter();
        $comparedFiltered = $group->variableHasFilter();

        /**
         * Both variable character groups are filtered
         * longer variable character groups has higher priority.
         */
        if ($variableFiltered && $comparedFiltered) {
            return $this->length <=> $group->length;
        }

        // Only this variable character group is filtered
        if ($variableFiltered && $comparedFiltered === false) {
            return 1;
        }

        // Only compared variable character group is filtered
        if ($variableFiltered === false && $comparedFiltered) {
            return -1;
        }

        // Consider both variable character groups are not filtered
        return 0;
    }

    /**
     * @return bool Return true if variable character group has filter
     */
    public function variableHasFilter(): bool
    {
        return strpos($this->string, ':') !== false;
    }
}
