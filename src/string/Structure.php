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
class Structure extends IterableStructure
{
    /** array Supported character group types */
    const CG_TYPES = [
        VariableFixedCG::class,
        VariableCG::class,
        FixedCG::class,
    ];

    /** @var string Input string */
    public $string;

    /**
     * Create string character group structure from string string.
     *
     * @param string $input Input string for string character group structure
     */
    public function __construct(string $input)
    {
        $this->string = $input;

        // Iterate until string is cleared
        while (strlen($input)) {
            foreach (self::CG_TYPES as $characterGroupType) {
                // Try to create character group
                if (!($group = $characterGroupType::fromString($input)) instanceof NullCG) {
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

        // Iterate maximum sized structure
        for ($index = 0; $index < $maxSize; $index++) {
            // Get compared/initial group or last compared character group is size mismatches
            $comparedGroup = $structure->groups[$index] ?? $structure->groups[$comparedStructureSize - 1];
            $initialGroup = $this->groups[$index] ?? $this->groups[$initialStructureSize - 1];

            // Define if character group with higher priority
            if (($return = $initialGroup->compare($comparedGroup)) !== 0) {
                return $return;
            }
        }

        // Structures are equal
        return 0;
    }

    /**
     * Get longest common prefix between strings.
     *
     * @param Structure $compared Compared character group structure
     *
     * @return string Strings Longest common prefix or empty string
     *
     */
    public function getCommonPrefix(Structure $compared)
    {
        $longestPrefix = '';

        /** @var AbstractCG $group Iterate longest structure character groups */
        foreach ($this->getShortestStructure($compared) as $index => $group) {
            $initialGroup = $this->groups[$index];
            $comparedGroup = $compared->groups[$index];

            // Get longest matching prefix between character groups.
            $prefix = $initialGroup->getCommonPrefix($comparedGroup);

            // Concatenate common prefix
            $longestPrefix .= $prefix;

            /**
             * If returned prefix is not equal to initial/compared character groups then it means
             * that it is shorter and we need to stop searching.
             */
            if ($prefix !== $initialGroup->getString() || $prefix !== $comparedGroup->getString()) {
                break;
            }
        }

        return $longestPrefix;
    }

    /**
     * Define shortest structure.
     *
     * @param Structure $compared Structure to compare
     *
     * @return Structure Shortest structure
     */
    private function getShortestStructure(Structure $compared): Structure
    {

        return $this->count() < $compared->count() ? $this : $compared;
    }
}
