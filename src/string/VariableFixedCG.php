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
class VariableFixedCG extends AbstractCharacterGroup
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
        $this->size = 2;

        parent::__construct($string, $length);

        // Parse internal character groups
        $this->variableCG = VariableCG::fromString($string);
        $this->fixedCG = FixedCG::fromString($string);
    }

    /**
     * @return bool True if character group is fixed length otherwise false
     */
    public function isFixed(): bool
    {
        return $this instanceof self;
    }

    /**
     * @inheritdoc
     */
    public function compare(AbstractCharacterGroup $group): int
    {
        // Equal character group types - return length comparison
        if ($this->isSameType($group)) {
            // Variable character groups with longer length has higher priority
            return $this->compareLength($group);
        }

        // Fixed character group has higher priority
        if ($group->isFixed()) {
            return -1;
        }

        /**
         * VariableFixed character group has higher priority than regular
         * variable character group.
         */
        return 1;
    }

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCharacterGroup $group): int
    {
        /** @var VariableFixedCG $group */

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
