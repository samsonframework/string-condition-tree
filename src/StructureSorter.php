<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 05.04.17 at 15:18
 */
namespace samsonframework\stringconditiontree;

/**
 * Parametrized strings sorting.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StructureSorter
{
    /** Variable length characters group */
    const G_VARIABLE = 0;

    /** Fixed length characters group */
    const G_FIXED = 1;

    /** @var string Parametrized string start marker */
    protected $parameterStartMarker;

    /** @var string Parametrized string end marker */
    protected $parameterEndMarker;

    /**
     * StructureSorter constructor.
     *
     * @param string $parameterStartMarker Parametrized string start marker
     * @param string $parameterEndMarker Parametrized string end marker
     */
    public function __construct(string $parameterStartMarker, string $parameterEndMarker)
    {
        $this->parameterStartMarker = $parameterStartMarker;
        $this->parameterEndMarker = $parameterEndMarker;
    }

    /**
     * Sort strings array considering PCG and NPCG string structure.
     *
     * @param array $input Input array for sorting
     *
     * @return array Sorted keys array
     */
    public function sortArrayByKeys(array $input): array
    {
        // Convert string array keys into structure arrays
        $prefixes = array_map([$this, 'getPrefixStructure'], array_keys($input));

        // Sort parametrized string array according sorting rules
        usort($prefixes, [$this, 'compareStringStructure']);

        // Restore initial strings sub-arrays
        $result = [];
        foreach ($prefixes as $sortingData) {
            $result[$sortingData[0][2]] = $input[$sortingData[0][2]];
        }
        return $result;
    }

    /**
     * Build string character group structure considering parametrized
     * and not parametrized character groups and their length(PCG, NPCG).
     *
     * @param string $prefix Prefix string
     *
     * @return array String character groups structure
     */
    protected function getPrefixStructure(string $prefix): array
    {
        /** @var array $structureMatrix String PCG(0)/NPCG(1) structure matrix for comparison */
        $structureMatrix = [];

        // Flags for showing current string character group
        /** @var bool $isPCG Flags showing PCG started */
        $isPCG = false;
        /** @var bool $isNPCG Flags showing NPCG started */
        $isNPCG = true;

        // Pointer to current CG to count string NPCG length
        $currentCG = 0;

        /**
         * TODO: Try to find PCG filter :... pattern and process it also as
         * PCG with filters should be prioritized over PSG without filter
         * even if filter is .*
         */

        // Iterate string by characters
        for ($i = 0, $length = strlen($prefix); $i < $length; $i++) {
            if (!$isPCG && $prefix{$i} === $this->parameterStartMarker) {
                $isPCG = true;
                $isNPCG = false;
                $structureMatrix[] = [0, 0, $prefix];
                $currentCG = &$structureMatrix[count($structureMatrix) - 1][1];
            } elseif ($isPCG && $prefix{$i} === $this->parameterEndMarker) {
                $isPCG = false;
                $isNPCG = true;
            } elseif ($isNPCG) {
                $isNPCG = false;
                $structureMatrix[] = [1, 0, $prefix];
                $currentCG = &$structureMatrix[count($structureMatrix) - 1][1];
            }

            // Store current character group length
            $currentCG++;
        }

        return $structureMatrix;
    }

    /**
     * Compare string structures.
     *
     * @param array $initial  Initial string structure
     * @param array $compared Compared string structure
     *
     * @return int Result of array elements comparison
     */
    protected function compareStringStructure(array $initial, array $compared): int
    {
        $maxStructureSize = $this->equalizeStructures($initial, $compared);

        // Iterate every structure group
        for ($i = 0; $i < $maxStructureSize; $i++) {
            // If initial structure has NPCG than it has higher priority
            if ($initial[$i][0] > $compared[$i][0]) {
                return -1;
            }

            // If compared structure has NPCG than it has higher priority
            if ($initial[$i][0] < $compared[$i][0]) {
                return 1;
            }

            // Compare NOT starting NPCG length
            if ($i > 0 && $initial[$i][0] === 1) {
                if ($initial[$i][1] > $compared[$i][1]) {
                    return -1;
                }

                if ($initial[$i][1] < $compared[$i][1]) {
                    return 1;
                }
            }

            // They are equal continue to next structure group comparison
        }

        // Compare fixed length CGS
        $return = $this->compareStructureLengths($initial, $compared, self::G_FIXED);

        // Fixed CGS are equal
        if ($return === 0) {
            // Compare variable length CGS
            $return = $this->compareStructureLengths($initial, $compared, self::G_VARIABLE);
        }

        return $return;
    }

    /**
     * Make CGS equals size.
     *
     * @param array $initial Initial CGS, will be changed
     * @param array $compared Compared CGS, will be changed
     *
     * @return int Longest CGS size(now they are both equal)
     */
    protected function equalizeStructures(array &$initial, array &$compared): int
    {
        $size = max(count($initial), count($compared));

        // Make structures same size preserving previous existing structure value
        for ($i = 1; $i < $size; $i++) {
            $this->fillMissingStructureGroup($initial, $i);
            $this->fillMissingStructureGroup($compared, $i);
        }

        return $size;
    }

    /**
     * Fill CSG with previous group value if not present.
     *
     * @param array $groups CSG for filling
     * @param int   $index  CSG index
     */
    private function fillMissingStructureGroup(array &$groups, int $index)
    {
        if (!array_key_exists($index, $groups)) {
            $groups[$index] = $groups[$index - 1];
        }
    }

    /**
     * Compare two character group structure(CGS) length and define
     * which one is longer.
     *
     * @param array $initial Initial CGS
     * @param array $compared Compared CGS
     * @param int   $type CGS type (Variable|Fixed length)
     *
     * @return int -1 if initial CGS longer
     *             0 if initial and compared CGS are equal
     *             1 if compared CGS longer
     */
    protected function compareStructureLengths(array $initial, array $compared, int $type = self::G_FIXED): int
    {
        // Iterate character group structures
        foreach ($initial as $index => $initialGroup) {
            $comparedGroup = $compared[$index];
            // Check if character group matches passed character group type
            if ($initialGroup[0] === $type) {
                $return = $this->compareLength($initialGroup, $comparedGroup, $type);

                // Compare character group length
                if ($return !== 0) {
                    return $return;
                }

                // Continue to next CGS
            }
        }

        // CGS have equal length
        return 0;
    }

    /**
     * Compare longer CGS considering that:
     * - Shortest fixed CGS should have higher priority
     * - Longest variable CGS should have higher priority
     *
     * @param array $initialGroup Initial CGS
     * @param array $comparedGroup Compared CGS
     * @param int   $type Fixed/Variable CGS
     *
     * @return int 0 if initial CGS is not longer than compared,
     *                  otherwise -1/1 depending on CGS type.
     */
    private function compareLength(array $initialGroup, array $comparedGroup, int $type)
    {
        // Compare character group length
        if ($initialGroup[1] > $comparedGroup[1]) {
            return ($type === self::G_FIXED ? 1 : -1);
        }

        if ($initialGroup[1] < $comparedGroup[1]) {
            return ($type === self::G_FIXED ? -1 : 1);
        }

        // Cannot define
        return 0;
    }
}
