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
class FixedCG extends AbstractCG
{
    /** string Character group matching regexp pattern matching group name */
    const PATTERN_GROUP = 'fixed';

    /** string Regular expression matching character group */
    const PATTERN_REGEXP = '[^{}]+';

    /** string Character group matching regexp pattern */
    const PATTERN = '(?<'.self::PATTERN_GROUP.'>'.self::PATTERN_REGEXP.')';

    /**
     * @inheritdoc
     */
    public function getCommonPrefix(AbstractCG $group): string
    {
        $prefix = '';

        // Convert strings to arrays
        $initialArray = str_split($this->string);
        $comparedArray = str_split($group->string);

        // Get shortest array
        $minSize = min(count($initialArray), count($comparedArray));

        // Iterate longest array
        for ($i = 0; $i < $minSize; $i++) {
            // Get existing character or empty string
            $initialChar = $initialArray[$i] ?? '';
            $comparedChar = $comparedArray[$i] ?? '';

            // On first mismatch - break
            if ($initialChar !== $comparedChar) {
                break;
            }

            $prefix .= $initialChar;
        }

        return $prefix;
    }

    /**
     * @inheritdoc
     */
    protected function compareLength(AbstractCG $group): int
    {
        /**
         * Shorter fixed character group has higher priority
         */
        return $group->length <=> $this->length;
    }
}
