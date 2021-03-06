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
     * @return null|AbstractCG|FixedCG|VariableCG Character group instance
     */
    public static function fromString(string &$input): ?AbstractCG
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
     * Compare character groups.
     *
     * @param AbstractCG|FixedCG|VariableCG|VariableFixedCG $group Compared character group
     *
     * @return int -1, 0, 1 Lower, equal, higher
     */
    abstract public function compare(AbstractCG $group): int;

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

    /**
     * Same character group type comparison.
     *
     * @param AbstractCG|FixedCG|VariableCG|VariableFixedCG $group Compared character group
     *
     * @return int -1, 0, 1 Character groups comparison result
     */
    abstract protected function compareLength(AbstractCG $group): int;
}
