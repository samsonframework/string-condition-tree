<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 02.03.17 at 13:25
 */
namespace samsonframework\stringconditiontree;

/**
 * Class StringConditionTree
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StringConditionTree
{
    /** @var array Output strings collection */
    protected $output = [];

    /** @var array Get input strings lengths */
    protected $stringLengths = [];

    /** @var TreeNode Resulting collection for debugging */
    protected $debug;

    /**
     * Compare strings by characters length.
     *
     * @param string $first
     * @param string $second
     *
     * @return int
     */
    protected function compareStrings(string $first, string $second): int
    {
        $firstLength = strlen($first);
        $secondLength = strlen($second);

        if ($firstLength === $secondLength) {
            return strcmp($first, $second);
        } else {
            return ($firstLength < $secondLength) ? -1 : 1;
        }
    }

    /**
     * Sort array by key string lengths.
     *
     * @param array $input Input array for sorting
     * @param int   $order Sorting order
     */
    protected function sortArrayByKeys(array &$input, int $order = SORT_ASC)
    {
        array_multisort(array_map('strlen', array_keys($input)), $order, $input);
    }

    protected function prepareInput(array $input): array
    {
        // Lower case and trim all strings in input
        $result = array_map('strtolower', array_map('trim', $input));

        // Sort strings by length ascending
        usort($result, [$this, 'compareStrings']);

        return $result;
    }

    public function process(array $input)
    {
        $input = $this->prepareInput($input);

        /**
         * We need to find first matching character that present at least at one two string
         * to start building tree. Otherwise there is no reason to build tree.
         */
        $this->debug = new TreeNode();

        $this->innerProcessor('@root', $input, $this->debug);

        return $this->debug;
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
        for ($z = 0, $length = strlen($shortestString); $z < $length; $z++) {
            // Compare characters with compared string
            if ($shortestString{$z} !== $longestString{$z}) {
                break; // Exit on first mismatching character
            }

            // Concatenate matching part of two strings
            $longestPrefix .= $initialString{$z};
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
        foreach ($array as $key => $values) {
            $lmpLength = strlen($key);
            for ($i = 0, $count = count($values); $i < $count; $i++) {
                $result[$key][$i] = substr($values[$i], $lmpLength);

                if ($result[$key][$i] === '') {
                    $result[$key][$i] = $selfMarker;
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

    protected function innerProcessor(string $prefix, array $input, TreeNode $result, $selfMarker = '@self')
    {
        $newChild = new TreeNode();
        $newChild->value = $prefix;
        $result->children[$prefix] = $newChild;

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

        /**
         * Sort LMPs(array keys) descending by key length
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
         * After filtering LMPs remove LMP from matched string arrays
         */
        $longestPrefixes = $this->removeKeyFromArrayStrings($longestPrefixes, $selfMarker);

        /**
         * If we have not found any LMPs then use input as an end with self marker pointers
         */
        if (count($longestPrefixes) === 0) {
            $longestPrefixes = array_combine($input, array_fill(0, count($input), [$selfMarker]));
        }

        /**
         * If we have self marker as an input string - create LMP for it
         */
        if (in_array($selfMarker, $input, true)) {
            $longestPrefixes[$selfMarker] = [];
        }

        /**
         * Recursively iterate current level LMPs
         */
        foreach ($longestPrefixes as $longestPrefix => $strings) {
            $this->innerProcessor($longestPrefix, $strings, $newChild);
        }
    }
}
