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
    /** array Supported character group types */
    const CG_TYPES = [
        FixedVariableFixedCG::class,
        VariableCG::class,
        FixedCG::class,
    ];

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

        // Iterate until input is cleared
        while (strlen($input)) {
            foreach (self::CG_TYPES as $characterGroupType) {
                if(($group = $characterGroupType::fromString($input)) !== null) {
                    $this->groups[] = $group;
                    // Reset CG type iterator to preserve order
                    break;
                }
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
