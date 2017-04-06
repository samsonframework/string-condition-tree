<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:28
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes string structure character group.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
abstract class AbstractCharacterGroup
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = '';

    /** string Regular expression matching character group */
    const PATTERN_REGEXP = '';

    /** string Character group matching regexp pattern */
    const PATTERN = '';

    /** @var int Character group length */
    public $length;

    /** @var string Character group string */
    public $string;

    /** @var int Character group size */
    protected $size = 1;

    /**
     * AbstractCharacterGroup constructor.
     *
     * @param string $string Character group string
     * @param int    $length Character group length
     */
    public function __construct(string $string, int $length = 0)
    {
        $this->string = $string;
        $this->length = $length;
    }

    /**
     * Create character group from input string.
     *
     * @param string $input Input string
     *
     * @return null|AbstractCharacterGroup|FixedCG|VariableCG|FixedVariableFixedCG Character group instance
     */
    public static function fromString(string &$input)
    {
        if (preg_match('/^'.static::PATTERN.'/', $input, $matches)) {
            // Replace only first occurrence of character group
            if (($pos = strpos($input, $matches[0])) !== false) {
                $input = substr_replace($input, '', $pos, strlen($matches[0]));

                $className = static::class;
                return new $className($matches[static::PATTERN_GROUP], strlen($matches[static::PATTERN_GROUP]));
            }
        }

        return null;
    }

    /**
     * @return bool True if character group is variable length otherwise false
     */
    public function isVariable(): bool
    {
        return $this instanceof VariableCG;
    }

    /**
     * Compare character groups.
     *
     * @param AbstractCharacterGroup $group Compared character group
     *
     * @return int -1, 0, 1 Lower, equal, higher
     */
    public function compare(AbstractCharacterGroup $group): int
    {
        // Equal character group types - return length comparison
        if ($this->isSameType($group)) {
            // Variable character groups with longer length has higher priority
            return $this->compareLength($group);
        }

        /**
         * Character groups are different:
         * Fixed character groups has higher priority,
         * variable character groups has lower priority
         */
        return $this->isFixed() ? 1 : -1;
    }

    /**
     * Check if compared character group has same type.
     *
     * @param AbstractCharacterGroup $group Compared character group
     *
     * @return bool True if character group has same type otherwise false
     */
    public function isSameType(AbstractCharacterGroup $group): bool
    {
        return get_class($group) === get_class($this);
    }

    /**
     * Compare this character group length to compared character group length.
     *
     * @param AbstractCharacterGroup $group Compared character group
     *
     * @return int -1, 0, 1 Character groups comparison result
     */
    abstract protected function compareLength(AbstractCharacterGroup $group): int;

    /**
     * @return bool True if character group is fixed length otherwise false
     */
    public function isFixed(): bool
    {
        return $this instanceof FixedCG;
    }
}
