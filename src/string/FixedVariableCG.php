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
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class FixedVariableCG extends AbstractCG
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'fixedVariable';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.FixedCG::PATTERN_REGEXP.VariableCG::PATTERN_REGEXP.')';

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
        $this->fixedCG = FixedCG::fromString($string);
        $this->variableCG = VariableCG::fromString($string);
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
            $prefix = $this->fixedCG->getCommonPrefix($group->fixedCG);

            if ($prefix === $this->fixedCG->getString() && $prefix === $group->fixedCG->getString()) {
                return $prefix . $this->variableCG->getCommonPrefix($group->variableCG);
            }

            return $prefix;
        }

        // Compare only first variable character groups
        if ($group instanceof FixedCG) {
            return $this->fixedCG->getCommonPrefix($group);
        }

        return '';
    }

    /**
     * @return bool True if character group is fixed length otherwise false
     */
    public function isFixed(): bool
    {
        return $this instanceof FixedVariableCG;
    }

    /**
     * @inheritdoc
     * @param AbstractCG|FixedVariableCG|FixedVariableCG|VariableFixedCG $group
     */
    protected function compareLength(AbstractCG $group): int
    {
        // Fixed CG are equal
        if (($return = $this->fixedCG->compare($group->fixedCG)) === 0) {
            // Compare variable character groups
            $return = $this->variableCG->compare($group->variableCG);
        }

        return $return;
    }
}
