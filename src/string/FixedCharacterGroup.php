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
class FixedCharacterGroup extends AbstractCharacterGroup
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'fixed';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>[^{}]+)';

    /**
     * @inheritdoc
     */
    public function compare(AbstractCharacterGroup $group): int
    {
        if ($this->isSameType($group)) { // Equal character group types - return length comparison
            return $this->length <=> $group->length;
        } elseif ($this->isFixed()) { // Fixed character groups has higher priority
            return 1;
        } else { // Variable character groups has lower priority
            return -1;
        }
    }
}
