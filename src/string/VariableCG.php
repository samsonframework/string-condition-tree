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
    /** string Regular expression named filter group */
    const PATTERN_FILTER_GROUP = 'filter';

    /** string Variable string filter pattern */
    const PATTER_FILTER = '/.*?:(?<'.self::PATTERN_FILTER_GROUP.'>[^}]+)/';

    /** string Regular expression named character group group */
    const PATTERN_GROUP = 'variable';

    /** string Regular expression matching character group */
    const PATTERN_REGEXP = '{.*?}';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.self::PATTERN_REGEXP.')';

    /** @var string Variable character group filter string */
    protected $filter;

    /**
     * @inheritdoc
     */
    public function __construct($string, $length = 0)
    {
        parent::__construct($string, $length);

        $this->filter = $this->getFilter();
    }

    /**
     * Get variable character group filter value.
     *
     * @return string Filter value or empty string
     */
    protected function getFilter(): string
    {
        if (preg_match(static::PATTER_FILTER, $this->string, $matches)) {
            return $matches[self::PATTERN_FILTER_GROUP];
        }

        return '';
    }

    /**
     * Whole variable length string should match.
     *
     * @inheritdoc
     */
    public function getCommonPrefix(AbstractCG $group): string
    {
        if ($this->isSameType($group)) {
            return $this->string === $group->string ? $this->string : '';
        }

        // Pass to compared
        return $group->getCommonPrefix($this);
    }

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCG $group): int
    {
        /** @var VariableCG $group */
        $variableFiltered = $this->isFiltered();
        $comparedFiltered = $group->isFiltered();

        /**
         * Both variable character groups are filtered
         * longer variable character groups has higher priority.
         */
        if ($variableFiltered && $comparedFiltered) {
            return strlen($this->filter) <=> strlen($group->filter);
        }

        // Only this variable character group is filtered
        if ($variableFiltered && $comparedFiltered === false) {
            return 1;
        }

        // Only compared variable character group is filtered
        if ($variableFiltered === false && $comparedFiltered) {
            return -1;
        }

        // 1 - 1 - longest
        // 1 - 0 - 1
        // 0 - 1 - -1
        // 0 - 0 - 0

        // Consider both variable character groups are not filtered
        return 0;
    }

    /**
     * @return bool Return true if variable character group has filter
     */
    protected function isFiltered(): bool
    {
        return $this->filter !== '';
    }
}
