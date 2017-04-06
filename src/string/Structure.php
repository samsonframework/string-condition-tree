<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:30
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes string character group structure(CGS).
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class Structure
{
    /** string Character group matching pattern */
    const PATTERN = '/' . FixedCharacterGroup::PATTERN . '|' . VariableCharacterGroup::PATTERN . '/';

    /** @var AbstractCharacterGroup[] */
    public $groups = [];

    /** @var string Input string */
    public $input;

    /**
     * Create string character group structure from input string.
     *
     * @param string $input Input string for string character group structure
     */
    public function __construct(string $input)
    {
        $this->input = $input;

        // Iterate input and find fixed/variable groups
        while (preg_match(self::PATTERN, $input, $matches)) {
            $input = str_replace($matches[0], '', $input);
            if (array_key_exists(VariableCharacterGroup::PATTERN_GROUP, $matches)) {
                $this->groups[] = new VariableCharacterGroup(strlen($matches[VariableCharacterGroup::PATTERN_GROUP]));
            } else {
                $this->groups[] = new FixedCharacterGroup(strlen($matches[FixedCharacterGroup::PATTERN_GROUP]));
            }
        }
    }

    /**
     * Compare structure character groups.
     *
     * @param Structure $structure Compared string structure group
     *
     * @return int Comparison result
     */
    public function compare(Structure $structure): int
    {
        $initialStructureSize = count($this->groups);
        $comparedStructureSize = count($structure->groups);
        $maxSize = max($initialStructureSize, $comparedStructureSize);

        for ($index = 0; $index < $maxSize; $index++) {
            // Get compared/initial group or last compared character group is size mismatches
            $comparedGroup = $structure->groups[$index] ?? $structure->groups[$comparedStructureSize - 1];
            $initialGroup = $this->groups[$index] ?? $this->groups[$initialStructureSize - 1];

            /**
             * Special opposite case when fixed CG goes after variable CG then
             * longer fixed CG should have higher priority as variable CG can possibly include
             * fixed CG.
             */
            if ($index > 0 && $index < $maxSize && $initialGroup->isFixed() && $initialGroup->isSameType($comparedGroup)) {
                if (($return = $initialGroup->compare($comparedGroup, true)) !== 0) {
                    return $return;
                }
            }

            if (($return = $initialGroup->compare($comparedGroup)) !== 0) {
                return $return;
            }
        }

        return 0;
    }
}
