<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 02.03.17 at 13:25
 */
namespace samsonframework\stringconditiontree;
use samsonframework\stringconditiontree\string\Structure;

/**
 * Class StringConditionTree
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

    /** @var array Collection of input string => identifier */
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
        $longestPrefixes = [];
        for ($i = 0, $length = count($input); $i < $length; $i++) {
            $initial = $input[$i];
            $foundLMP = false;
            for ($j = 0; $j < $length; $j++) {
                $compared = $input[$j];
                if ($compared !== $initial) {
                    $longestMatchedPrefix = $this->getLongestMatchingPrefix($initial, $compared);

                    // We have found at least one matching character between strings
                    if ($longestMatchedPrefix !== '') {
                        $foundLMP = true;
                        $this->addUniqueToArray($initial, $longestPrefixes[$longestMatchedPrefix]);
                    }
                }
            }

            // Add initial string as LMP
            if ($foundLMP === false) {
                $this->addUniqueToArray($initial, $longestPrefixes[$initial]);
            }
        }

        return $longestPrefixes;
    }

    /**
     * Find longest matching prefix between two strings.
     *
     * @param string $initialString  Initial string
     * @param string $comparedString Compared string
     *
     * @return string Longest matching prefix
     */
    protected function getLongestMatchingPrefix(string $initialString, string $comparedString): string
    {
        // Iterate and compare how string matches
        $longestPrefix = '';

        // Define shortest & longest strings to avoid errors
        $initialLength = strlen($initialString);
        $comparedLength = strlen($comparedString);

        // Define shortest and longest strings to avoid character matching errors
        $shortestString = $initialLength < $comparedLength ? $initialString : $comparedString;
        $longestString = $initialLength >= $comparedLength ? $initialString : $comparedString;

        // Iterate initial string characters
        $isPattern = false;
        $parametrizedPrefix = '';
        for ($z = 0, $length = strlen($shortestString); $z < $length; $z++) {
            // Pattern support
            if ($isPattern || $shortestString{$z} === $this->parameterStartMarker) {
                $isPattern = true;

                // Concatenate longest matching prefix
                $parametrizedPrefix .= $shortestString{$z};

                // Compare characters with compared string
                if ($shortestString{$z} !== $longestString{$z}) {
                    break;
                }

                // If pattern id closed unset flag for special behaviour
                if ($shortestString{$z} === $this->parameterEndMarker) {
                    // If parametrized part ended append to longest matching prefix
                    $longestPrefix .= $parametrizedPrefix;
                    // Clear parametrized prefix as we can have other parametrized parts
                    $parametrizedPrefix = '';
                    // Reset parametrized flag
                    $isPattern = false;
                }
            } else {
                // Compare characters with compared string
                if ($shortestString{$z} !== $longestString{$z}) {
                    break; // Exit on first mismatching character
                }

                // Concatenate matching part of two strings
                $longestPrefix .= $initialString{$z};
            }
        }

        return $longestPrefix;
    }

    /**
     * Add only unique value to array.
     *
     * @param mixed $value  Unique value
     * @param array $array  Array for adding unique value
     * @param bool  $strict Strict uniqueness check
     *
     * @return bool True if unique value was added
     * @see in_array();
     *
     */
    protected function addUniqueToArray($value, &$array, bool $strict = true)
    {
        // Create array if not array is passed
        if (!is_array($array)) {
            $array = [];
        }

        // Add value to array if it is unique
        if (!in_array($value, $array, $strict)) {
            $array[] = $value;

            return true;
        }

        // Value is already in array
        return false;
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
            $structures[$string] = new Structure($string);
        }

        usort($structures, function (Structure $initial, Structure $compared) {
            return $initial->compare($compared);
        });

        $structures = array_reverse($structures);

        // Restore initial strings sub-arrays
        $result = [];
        foreach ($structures as $structure) {
            $result[$structure->input] = $input[$structure->input];
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
