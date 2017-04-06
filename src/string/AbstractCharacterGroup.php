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

    /**
     * AbstractCharacterGroup constructor.
     *
     * @param int $length Character group length
     */
    public function __construct(int $length = 0)
    {
        $this->length = $length;
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
     * @return int Comparison result
     */
    abstract public function compare(AbstractCharacterGroup $group): int;
}
