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
    const PATTERN = '/' . FixedVariableFixedCG::PATTERN . '|' . FixedCG::PATTERN . '|' . VariableCG::PATTERN . '/';

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
            $matches = array_filter($matches);
            // Replace only first occurrence of character group
            if (($pos = strpos($input, $matches[0])) !== false) {
                $input = substr_replace($input, '', $pos, strlen($matches[0]));
            }

            if (array_key_exists(FixedVariableFixedCG::PATTERN_GROUP, $matches)) {
                $this->groups[] = new FixedVariableFixedCG(
                    $matches[FixedVariableFixedCG::PATTERN_GROUP],
                    strlen($matches[FixedVariableFixedCG::PATTERN_GROUP])
                );
            } elseif (array_key_exists(VariableCG::PATTERN_GROUP, $matches)) {
                $this->groups[] = new VariableCG(
                    $matches[VariableCG::PATTERN_GROUP],
                    strlen($matches[VariableCG::PATTERN_GROUP])
                );
            } else {
                $this->groups[] = new FixedCG(
                    $matches[FixedCG::PATTERN_GROUP],
                    strlen($matches[FixedCG::PATTERN_GROUP])
                );
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

        $priorityMatrix = [];

        // Iterate maximum sized structure
        for ($index = 0; $index < $maxSize; $index++) {
            // Get compared/initial group or last compared character group is size mismatches
            $comparedGroup = $structure->groups[$index] ?? $structure->groups[$comparedStructureSize - 1];
            $initialGroup = $this->groups[$index] ?? $this->groups[$initialStructureSize - 1];

            // Define if initial character group has higher priority
            $priorityMatrix[] = $initialGroup->compare($comparedGroup) * ($initialGroup->isFixed() || $comparedGroup->isFixed() ? $maxSize : 1);
        }

        /**
         * Possible structures
         *
         * var|fixed
         * var|fixed|var
         * fixed|var
         * fixed|var|fixed
         */

        $return = array_sum($priorityMatrix);
        if ($return > 0) {
            return 1;
        } elseif ($return < 0) {
            return -1;
        }

        return 0;
    }
}
