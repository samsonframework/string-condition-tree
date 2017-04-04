<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 02.03.17 at 13:25
 */
namespace samsonframework\stringconditiontree;
use phpDocumentor\Reflection\Types\Integer;

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

    /** @var TreeNode Resulting collection for debugging */
    protected $debug;

    /** @var string Parametrized string start marker */
    protected $parameterStartMarker = self::PARAMETER_START;

    /** @var string Parametrized string end marker */
    protected $parameterEndMarker = self::PARAMETER_END;

    /**
     * StringConditionTree constructor.
     *
     * @param string $parameterStartMarker Parametrized string start marker
     * @param string $parameterEndMarker Parametrized string end marker
     */
    public function __construct(string $parameterStartMarker = self::PARAMETER_START, string $parameterEndMarker = self::PARAMETER_END)
    {
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
        /**
         * We need to find first matching character that present at least at one two string
         * to start building tree. Otherwise there is no reason to build tree.
         */
        $this->innerProcessor(self::ROOT_NAME, $input, $this->debug = new TreeNode());

        $this->debug = $this->debug->children[self::ROOT_NAME];

        return $this->debug;
    }

    /**
     * Prefix length counter for array sorting callback to sort by prefix length and put
     * parametrized prefixed at the end.
     *
     * @param string $prefix Prefix string
     *
     * @return int Prefix length
     */
    protected function prefixLength(string $prefix): int
    {
        return strpos($prefix, self::PARAMETER_START) !== false ? PHP_INT_MAX : strlen($prefix);
    }

    /**
     * Sort array by key string lengths.
     *
     * @param array $input Input array for sorting
     * @param int   $order Sorting order
     */
    protected function sortArrayByKeys(array &$input, int $order = SORT_ASC)
    {
        array_multisort(array_map([$this, 'prefixLength'], array_keys($input)), $order, $input);
    }

    /**
     * Add only unique value to array.
     *
     * @param mixed $value  Unique value
     * @param array $array  Array for adding unique value
     * @param bool  $strict Strict uniqueness check
     *
     * @see in_array();
     *
     * @return bool True if unique value was added
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
        } else { // Value is already in array
            return false;
        }
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
            // TODO: Probably can be optimized
            if ($isPattern || $shortestString{$z} === $this->parameterStartMarker) {
                $isPattern = true;

                // Concatenate longest matching prefix
                $parametrizedPrefix .= $shortestString{$z};

                // Compare characters with compared string
                if ($shortestString{$z} !== $longestString{$z}) {
                    // Clear pattern as pattern characters mismatch
                    $parametrizedPrefix = '';
                    break;
                }

                // If pattern id closed unset flag fro special behaviour
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
     * Remove key string from the beginning of all sub-array strings.
     *
     * @param array  $array Input array of key => [keyStrings...]
     *
     * @param string $selfMarker Marker for storing self pointer
     *
     * @return array Processed array with removed keys from beginning of sub arrays
     */
    protected function removeKeyFromArrayStrings(array $array, string $selfMarker): array
    {
        $result = [];
        /** @var string[] $values */
        foreach ($array as $key => $values) {
            $lmpLength = strlen((string)$key);
            foreach ($values as $string) {
                $newString = substr($string, $lmpLength);

                if ($newString === false || $newString === '' || $string === $selfMarker) {
                    $result[$key][] = $selfMarker;
                } else {
                    $result[$key][] = $newString;
                }
            }
        }

        return $result;
    }

    /**
     * Find all duplication of source array values in compared array and remove them.
     *
     * @param array $source Source array
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
     * Recursive string similarity tree builder.
     *
     * @param string   $prefix
     * @param array    $input
     * @param TreeNode $result
     * @param string   $selfMarker
     */
    protected function innerProcessor(string $prefix, array $input, TreeNode $result, $selfMarker = self::SELF_NAME)
    {
        // Create tree node
        $newChild = $result->append($prefix);

        /**
         * Iterate all combinations of strings and group by LMP
         */
        $longestPrefixes = [];
        for ($i = 0, $count = count($input); $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $longestMatchedPrefix = $this->getLongestMatchingPrefix($input[$i], $input[$j]);

                // We have found at least one matching character between strings
                if ($longestMatchedPrefix !== '') {
                    $this->addUniqueToArray($input[$i], $longestPrefixes[$longestMatchedPrefix]);
                    $this->addUniqueToArray($input[$j], $longestPrefixes[$longestMatchedPrefix]);
                }
            }
        }

        if (array_key_exists('{param}-{parameter}', $longestPrefixes)) {
            var_dump(1);
        }


        /**
         * Sort LMPs(array keys) ascending by key length
         */
        $this->sortArrayByKeys($longestPrefixes);

        /**
         * Iterate all sorted LMP strings and remove duplicates from LMP string ordered lower
         */
        $keys = array_keys($longestPrefixes);
        for ($i = 0, $length = count($keys); $i < $length; $i++) {
            for ($j = $i + 1; $j < $length; $j++) {
                $this->removeDuplicatesInSubArray($longestPrefixes[$keys[$i]], $longestPrefixes[$keys[$j]]);
            }
        }

        // Remove empty LMPs as they are included in smaller LMPs
        $longestPrefixes = array_filter($longestPrefixes);

        /**
         * Search for input string that do not have LMP, and add missing as LMP
         */
        foreach ($input as $string) {
            $found = false;

            if ($string !== $selfMarker) {
                foreach ($longestPrefixes as $strings) {
                    if (in_array($string, $strings, true)) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $longestPrefixes[$string] = [$selfMarker];
                }
            }
        }

        /**
         * After filtering LMPs remove LMP from matched string arrays
         */
        $longestPrefixes = $this->removeKeyFromArrayStrings($longestPrefixes, $selfMarker);

        /**
         * Sort LMPs(array keys) ascending by key length
         */
        $this->sortArrayByKeys($longestPrefixes);

        /**
         * If we have self marker as an input string - create LMP for it
         */
        if (in_array($selfMarker, $input, true)) {
            $longestPrefixes = array_merge([$selfMarker => []], $longestPrefixes);
        }

        /**
         * Recursively iterate current level LMPs
         */
        foreach ($longestPrefixes as $longestPrefix => $strings) {
            $this->innerProcessor((string)$longestPrefix, $strings, $newChild);
        }
    }
}
