<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:34
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes character group with next structure:
 * - variable length character group
 * - fixed length character group
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class VariableFixedCG extends AbstractCG
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'variableFixed';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.VariableCG::PATTERN_REGEXP.FixedCG::PATTERN_REGEXP.')';

    /** @var VariableCG */
    protected $variableCG;

    /** @var FixedCG */
    protected $fixedCG;

    /**
     * @inheritdoc
     */
    public function __construct(string $string, int $length = 0)
    {
        parent::__construct($string, $length);

        // Parse internal character groups
        $this->variableCG = VariableCG::fromString($string);
        $this->fixedCG = FixedCG::fromString($string);
    }

    /**
     * Get variable character group common prefix, if exists then
     * append fixed character group prefix.
     *
     * @inheritdoc
     */
    public function getCommonPrefix(AbstractCG $group): string
    {
        // Get common prefix as concatenation of variable and fixed character groups common prefixes
        if ($this->isSameType($group)) {
            /** @var VariableFixedCG $group */
            if (($prefix = $this->variableCG->getCommonPrefix($group->variableCG)) !== ''){
                return $prefix . $this->fixedCG->getCommonPrefix($group->fixedCG);
            }
        }

        // Compare only first variable character groups
        if ($group instanceof VariableCG) {
            return $this->variableCG->getCommonPrefix($group);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function compare(AbstractCG $group): int
    {
        // Equal character group types - return length comparison
        if ($this->isSameType($group)) {
            // Variable character groups with longer length has higher priority
            return $this->compareLength($group);
        }

        /**
         * Fixed character group has higher priority
         */
        return $group->isFixed() ? -1 : 1;
    }

    /**
     * @inheritdoc
     * @param VariableFixedCG $group
     */
    protected function compareLength(AbstractCG $group): int
    {
        // Fixed CG are equal
        if (($return = $this->compareFixed($group)) === 0) {
            // Compare variable character groups
            $return = $this->variableCG->compare($group->variableCG);
        }

        return $return;
    }

    /**
     * Longer fixed character group has higher priority.
     *
     * @param VariableFixedCG $group Compared character group
     *
     * @return int Comparison result
     */
    private function compareFixed(VariableFixedCG $group): int
    {
        // Opposite fixed CG comparison
        return $this->fixedCG->length <=> $group->fixedCG->length;
    }
}
