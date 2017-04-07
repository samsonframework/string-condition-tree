<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 02.03.17 at 13:25
 */
namespace samsonframework\stringconditiontree;

use samsonframework\stringconditiontree\string\CommonPrefix;
use samsonframework\stringconditiontree\string\Structure;

/**
 * Class StringConditionTree
 *
 * TODO: Remove variable character group markers, move LMP search to string.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StringConditionTree
{
    /** Tree node root element identifier, needed for recursion */
    const ROOT_NAME = '';

    /** Final tree node branch identifier */
    const SELF_NAME = '@self';

    /** String parameter start marker */
    const PARAMETER_START = '{';

    /** String parameter end marker */
    const PARAMETER_END = '}';

    /** Parameter sorting length value for counting */
    const PARAMETER_COF = 2000;

    /** @var TreeNode Resulting collection for debugging */
    protected $debug;

    /** @var array Collection of string string => identifier */
    protected $source;

    /** @var string Parametrized string start marker */
    protected $parameterStartMarker = self::PARAMETER_START;

    /** @var string Parametrized string end marker */
    protected $parameterEndMarker = self::PARAMETER_END;

    /**
     * StringConditionTree constructor.
     *
     * @param string               $parameterStartMarker Parametrized string start marker
     * @param string               $parameterEndMarker   Parametrized string end marker
     */
    public function __construct(
        string $parameterStartMarker = self::PARAMETER_START,
        string $parameterEndMarker = self::PARAMETER_END
    ) {
        $this->parameterStartMarker = $parameterStartMarker;
        $this->parameterEndMarker = $parameterEndMarker;
    }

    /**
     * Build similarity strings tree.
     *
     * @param array $input Collection of strings
     *
     * @return TreeNode Resulting similarity strings tree
     */
    public function process(array $input): TreeNode
    {
        $this->source = $input;

        /**
         * We need to find first matching character that present at least at one two string
         * to start building tree. Otherwise there is no reason to build tree.
         */
        $this->innerProcessor(self::ROOT_NAME, array_keys($input), $this->debug = new TreeNode());

        return $this->debug->children[self::ROOT_NAME];
    }

    /**
     * Recursive string similarity tree builder.
     *
     * @param string   $prefix
     * @param array    $input
     * @param TreeNode $result
     */
    protected function innerProcessor(string $prefix, array $input, TreeNode $result)
    {
        // Create tree node. Pass string identifier if present
        $newChild = $result->append($prefix, $this->source[$result->fullValue . $prefix] ?? '');

        /**
         * Iterate all combinations of strings and group by LMP, also if no LMP is
         * found consider strings as LMP itself
         */
        $longestPrefixes = $this->getLMPCollection($input);

        /**
         * Sort LMPs(array keys) ascending by key length
         */
        $longestPrefixes = $this->sortArrayByKeys($longestPrefixes);

        /**
         * Iterate all sorted LMP strings and remove duplicates from LMP string ordered lower
         */
        $this->filterLMPStrings($longestPrefixes);

        /**
         * After filtering LMPs remove LMP from matched string arrays
         */
        $longestPrefixes = $this->removeKeyFromArrayStrings($longestPrefixes);

        /**
         * Recursively iterate current level LMPs
         */
        foreach ($longestPrefixes as $longestPrefix => $strings) {
            $this->innerProcessor((string)$longestPrefix, $strings, $newChild);
        }
    }

    /**
     * Get collection of grouped longest matching prefixes with strings sub-array.
     *
     * @param array $input Input strings array
     *
     * @return array Longest matching prefixes array
     */
    protected function getLMPCollection(array $input): array
    {
        $structures = $this->sortStructures($input);

        $longestPrefixes = [];

        // Iterate sorted character group structures
        foreach ($structures as $initialStructure) {
            $foundLMP = false;
            // Iterate all character group structures again
            foreach ($structures as $comparedStructure) {
                // Ignore same structures
                if ($initialStructure === $comparedStructure) {
                    continue;
                }

                // Get common longest prefix between structures
                if (($longestPrefix = $initialStructure->getCommonPrefix($comparedStructure)) !== '') {
                    $foundLMP = true;
                    $foundExisting = false;
                    foreach ($longestPrefixes as $prefix => $strings) {
                        if ((new Structure($prefix))->getCommonPrefix(new Structure($longestPrefix)) === $prefix) {
                            $longestPrefixes[$prefix][] = $comparedStructure->string;
                            $foundExisting = true;
                            break;
                        }
                    }

                    if (!$foundExisting) {
                        $longestPrefixes[$longestPrefix][] = $comparedStructure->string;
                    }
                }
            }

            // We have not found longest common prefix with no strings - add as separate
            if (!$foundLMP) {
                $longestPrefixes[$initialStructure->string][] = $initialStructure->string;
            }
        }

        // Clear duplicates
        foreach ($longestPrefixes as $prefix => &$strings) {
            $longestPrefixes[$prefix] = array_unique($strings);
        }

        return $longestPrefixes;
    }

    /**
     * Sort structures array.
     *
     * @param array $strings Input strings array
     *
     * @return Structure[] Sorted structures array
     */
    protected function sortStructures(array $strings): array
    {
        // Create structures
        $structures = [];
        foreach ($strings as $string) {
            $structures[] = new Structure($string);
        }

        // Sort structures
        usort($structures, function (Structure $initial, Structure $compared) {
            return $initial->compare($compared);
        });

        // Sort descending
        return array_reverse($structures);
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
        /** @var Structure[] $structures */
        $structures = [];
        foreach (array_keys($input) as $string) {
            $structures[] = new Structure($string);
        }

        usort($structures, function (Structure $initial, Structure $compared) {
            return $initial->compare($compared);
        });

        $structures = array_reverse($structures);

        // Restore initial strings sub-arrays
        $result = [];
        foreach ($structures as $structure) {
            $result[$structure->string] = $input[$structure->string];
        }

        return $result;
    }

    /**
     * Iterate LMP and remove duplicate strings in other LMPs.
     *
     * @param array $prefixes LMP collection, returning value
     */
    protected function filterLMPStrings(array &$prefixes)
    {
        $keys = array_keys($prefixes);
        for ($i = 0, $length = count($keys); $i < $length; $i++) {
            for ($j = $i + 1; $j < $length; $j++) {
                $this->removeDuplicatesInSubArray($prefixes[$keys[$i]], $prefixes[$keys[$j]]);
            }
        }
    }

    /**
     * Find all duplication of source array values in compared array and remove them.
     *
     * @param array $source   Source array
     * @param array $compared Compared array for filtering duplicates
     */
    protected function removeDuplicatesInSubArray(array $source, array &$compared)
    {
        foreach ($source as $value) {
            foreach ($compared as $key => &$subValue) {
                if ($subValue === $value) {
                    unset($compared[$key]);
                }
            }
        }
    }

    /**
     * Remove key string from the beginning of all sub-array strings.
     *
     * @param array $array Input array of key => [keyStrings...]
     *
     * @return array Processed array with removed keys from beginning of sub arrays
     */
    protected function removeKeyFromArrayStrings(array $array): array
    {
        $processed = [];
        /** @var string[] $stringsCollection */
        foreach ($array as $keyString => $stringsCollection) {
            $lmpLength = strlen((string)$keyString);
            foreach ($stringsCollection as $stringValue) {
                // Remove LMP from string
                $newString = substr($stringValue, $lmpLength);

                // This string has something left besides lmp
                if ($newString !== false && $newString !== '') {
                    $processed[$keyString][] = $newString;
                } elseif (array_key_exists($keyString, $processed) === false) {
                    // Add empty array to LMP if missing
                    $processed[$keyString] = [];
                }
            }
        }

        return $processed;
    }
}
