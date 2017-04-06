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
    /** @var int Character group length */
    public $length;

    /** @var string Character group string */
    public $string;

    /**
     * AbstractCharacterGroup constructor.
     *
     * @param int $length Character group length
     */
    public function __construct(string $string, int $length = 0)
    {
        $this->string = $string;
        $this->length = $length;
    }

    /**
     * @return bool True if character group is variable length otherwise false
     */
    public function isVariable(): bool
    {
        return $this instanceof VariableCharacterGroup;
    }

    /**
     * Compare character groups.
     *
     * @param AbstractCharacterGroup $group Compared character group
     *
     * @param bool                   $afterVariable Flag that this character group goes after variable character group
     *
     * @return int Comparison result
     */
    public function compare(AbstractCharacterGroup $group, bool $afterVariable = false): int
    {
        // Equal character group types - return length comparison
        if ($this->isSameType($group)) {
            if ($this->isFixed() && $afterVariable === false) {
                // Fixed character groups with longer length has lower priority
                return $group->length <=> $this->length;
            }

            // Variable character groups with longer length has higher priority
            return $this->length <=> $group->length;
        }

        // Fixed character groups has higher priority
        if ($this->isFixed()) {
            return 1;
        }

        // Variable character groups has lower priority
        return -1;
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
     * @return bool True if character group is fixed length otherwise false
     */
    public function isFixed(): bool
    {
        return $this instanceof FixedCharacterGroup;
    }
}
