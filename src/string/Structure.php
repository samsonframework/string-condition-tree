<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:30
 */
namespace samsonframework\stringconditiontree\string;

use samsonframework\stringconditiontree\AbstractIterable;

/**
 * This class describes string character group structure(CGS).
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class Structure extends AbstractIterable
{
    /** string Internal collection name */
    protected const COLLECTION_NAME = 'groups';

    /** array Supported character group types */
    const CG_TYPES = [
        FixedVariableCG::class,
        VariableFixedCG::class,
        VariableCG::class,
        FixedCG::class,
    ];

    /** @var AbstractCG[] */
    public $groups = [];

    /** @var string Input string */
    public $string;

    /**
     * Create string character group structure from string string.
     *
     * @param string $input Input string for string character group structure
     */
    public function __construct(string $input)
    {
        parent::__construct();

        $this->string = $input;

        // Iterate until string is cleared
        while (strlen($input)) {
            foreach (self::CG_TYPES as $characterGroupType) {
                // Try to create character group
                if (($group = $characterGroupType::fromString($input)) !== null) {
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
            if ($this->isStringNotMatchingAnyCG($prefix, $initialGroup, $comparedGroup)) {
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

    /**
     * Define if string does not match any of the passed character groups.
     *
     * @param string     $string String for comparison
     * @param AbstractCG $initialGroup Initial character group
     * @param AbstractCG $comparedGroup Compared character group
     *
     * @return bool True if string not matching any of character groups
     */
    private function isStringNotMatchingAnyCG(string $string, AbstractCG $initialGroup, AbstractCG $comparedGroup): bool
    {
        return $string !== $initialGroup->getString() || $string !== $comparedGroup->getString();
    }

    /**
     * @return string Structure input string
     */
    public function getString(): string
    {
        return $this->string;
    }
}
