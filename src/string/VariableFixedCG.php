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
    protected function compareLength(AbstractCharacterGroup $group): int
    {
        /** @var VariableFixedCG $group */

        // Fixed CG are equal
        if (($return = $this->compareFixed($group)) === 0) {
            $variableFiltered = $this->variableHasFilter();
            $comparedFiltered = $group->variableHasFilter();

            // Filtered variable character group has priority
            if ($variableFiltered) {
                $return = 1;
                // Both are filtered
                if ($comparedFiltered) {
                    // Longer Variable character group has higher priority
                    $return = $this->variableCG->length <=> $group->variableCG->length;
                }
            } elseif ($comparedFiltered) {
                $return = -1;
            }
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

    /**
     * @return bool Return true if variable character group has filter
     */
    public function variableHasFilter(): bool
    {
        return strpos($this->variableCG->string, ':') !== false;
    }
}
