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

        // Shorter FCG has higher priority
        $return = $group->fixedCG->length <=> $this->fixedCG->length;

        // Fixed CG are equal
//        if ($return === 0) {
//            // Longer first VCG has higher priority
//            $return = $this->variableCG->length <=> $group->variableCG->length;
//        }
        // TODO: Check for filtering pattern and VCG with filter has priority
        // TODO: But how to compare filters if present?

        return $return;
    }
}
