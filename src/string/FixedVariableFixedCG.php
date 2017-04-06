<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:34
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes character group with next structure:
 * - fixed length character group
 * - variable length character group
 * - fixed length character group
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FixedVariableFixedCG extends AbstractCharacterGroup
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'fixedVariableFixed';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'
    .FixedCG::PATTERN_REGEXP
    .VariableCG::PATTERN_REGEXP
    .FixedCG::PATTERN_REGEXP
    .')';

    /** @var FixedCG */
    protected $firstFixedCG;

    /** @var VariableCG */
    protected $variableCG;

    /** @var FixedCG */
    protected $lastFixedCG;

    /**
     * @inheritdoc
     */
    public function __construct(string $string, int $length = 0)
    {
        parent::__construct($string, $length);

        // Parse internal character groups
        $this->firstFixedCG = FixedCG::fromString($string);
        $this->variableCG = VariableCG::fromString($string);
        $this->lastFixedCG = FixedCG::fromString($string);
    }

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCharacterGroup $group): int
    {
        /** @var FixedVariableFixedCG $group */

        // Compare fixed character groups
        if (($return = $this->compareFixed($group)) === 0) {
            // Longer VCG has higher priority
            $return = $this->variableCG->length <=> $group->variableCG->length;
        }

        return $return;
    }

    /**
     * Compare fixed character groups.
     *
     * @param FixedVariableFixedCG $group Compared character group
     *
     * @return int Comparison result
     */
    private function compareFixed(FixedVariableFixedCG $group): int
    {
        if (($return = $this->compareFirstFixed($group)) === 0) {
            $return = $this->compareLastFixed($group);
        }

        return $return;
    }

    /**
     * Shorter first fixed character group has higher priority
     *
     * @param FixedVariableFixedCG $group Compared character group
     *
     * @return int Comparison result
     */
    private function compareFirstFixed(FixedVariableFixedCG $group): int
    {
        // Regular fixed CG comparison
        return $this->firstFixedCG->compareLength($group->firstFixedCG);
    }

    /**
     * Longer last fixed character group has higher priority
     *
     * @param FixedVariableFixedCG $group Compared character group
     *
     * @return int Comparison result
     */
    private function compareLastFixed(FixedVariableFixedCG $group): int
    {
        // Opposite comparison
        return $this->lastFixedCG->length <=> $group->lastFixedCG->length;
    }
}
