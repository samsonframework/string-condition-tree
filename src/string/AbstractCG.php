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
abstract class AbstractCG
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = '';

    /** string Regular expression matching character group */
    const PATTERN_REGEXP = '';

    /** string Character group matching regexp pattern */
    const PATTERN = '';

    /** @var int Character group length */
    protected $length;

    /** @var string Character group string */
    protected $string;

    /**
     * AbstractCharacterGroup constructor.
     *
     * @param string $string Character group string
     * @param int    $length Character group length
     */
    public function __construct(string $string, int $length = null)
    {
        $this->string = $string;
        $this->length = $length ?? strlen($string);
    }

    /**
     * Create character group from string string.
     *
     * @param string $input Input string
     *
     * @return NullCG|AbstractCG|FixedCG|VariableCG Character group instance
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

        return new NullCG();
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
     * @param AbstractCG|FixedCG|VariableCG|VariableFixedCG $group Compared character group
     *
     * @return int -1, 0, 1 Lower, equal, higher
     */
    public function compare(AbstractCG $group): int
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
     * @param AbstractCG $group Compared character group
     *
     * @return bool True if character group has same type otherwise false
     */
    public function isSameType(AbstractCG $group): bool
    {
        return get_class($group) === get_class($this);
    }

    /**
     * Compare this character group length to compared character group length.
     *
     * @param AbstractCG|FixedCG|VariableCG|VariableFixedCG $group Compared character group
     *
     * @return int -1, 0, 1 Character groups comparison result
     */
    abstract protected function compareLength(AbstractCG $group): int;

    /**
     * @return bool True if character group is fixed length otherwise false
     */
    public function isFixed(): bool
    {
        return $this instanceof FixedCG;
    }

    /**
     * Get two character groups longest common prefix.
     *
     * @param AbstractCG|FixedCG|VariableCG|VariableFixedCG $group Compared character group
     *
     * @return string Longest common prefix or empty string
     */
    abstract public function getCommonPrefix(AbstractCG $group): string;

    /**
     * @return string Character group string
     */
    public function getString(): string
    {
        return $this->string;
    }
}
