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

        // Shorter first FCG has higher priority
        $return = $group->firstFixedCG->length <=> $this->firstFixedCG->length;

        // First FCG are equal
        if ($return === 0) {
            // Longer last FCG has higher priority
            $return = $this->lastFixedCG->length <=> $group->lastFixedCG->length;

            // Last FCG are equal
            if ($return === 0) {
                // Longer VCG has higher priority
                $return = $this->variableCG->length <=> $group->variableCG->length;
            }
        }

        return $return;
    }
}
