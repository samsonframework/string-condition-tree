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
        $maxStructureSize = max(count($initial), count($compared));

        // Make structures same size preserving previous existing structure value
        for ($i = 1; $i < $maxStructureSize; $i++) {
            if (!array_key_exists($i, $initial)) {
                $initial[$i] = $initial[$i - 1];
            }
            if (!array_key_exists($i, $compared)) {
                $compared[$i] = $compared[$i - 1];
            }
        }

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

        // If both structures are equal compare lengths of NPCG
        for ($i = 0; $i < $maxStructureSize; $i++) {
            // If current CG is NPCG
            if ($initial[$i][0] === 1) {
                if ($initial[$i][1] > $compared[$i][1]) {
                    return 1;
                }

                if ($initial[$i][1] < $compared[$i][1]) {
                    return -1;
                }
            }

            // Current NPCG character groups have equal length - continue
        }

        // If both structures are equal and NPCG length are equal - compare lengths of PCG
        for ($i = 0; $i < $maxStructureSize; $i++) {
            // If current CG is PCG
            if ($initial[$i][0] === 0) {
                if ($initial[$i][1] > $compared[$i][1]) {
                    return -1;
                }

                if ($initial[$i][1] < $compared[$i][1]) {
                    return 1;
                }
            }

            // Current PCG character groups have equal length - continue
        }

        // Structures are absolutely equal
        return 0;
    }
}
