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
        $return = $this->innerProcessor($input);

        return $return;
    }

    protected function getLongestMatchingPrefix(string $initialString, string $comparedString): string
    {
        // Iterate and compare how string matches
        $longestPrefix = '';

        // Define shortest & longest strings to avoid errors
        $initialLength = strlen($initialString);
        $comparedLength = strlen($comparedString);

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

    protected function innerProcessor(array $input)
    {
        $matched = [];

        foreach ($input as $initialString) {
            $shortestCommonPrefix = '';

            foreach ($input as $comparedString) {
                $longestCommonPrefix = $this->getLongestMatchingPrefix($initialString, $comparedString);

                if ($shortestCommonPrefix === '' || strlen($shortestCommonPrefix) > strlen($longestCommonPrefix)) {
                    $shortestCommonPrefix = $longestCommonPrefix;
                }
            }

            // If we have found shortest common prefix from longest ones
            if ($shortestCommonPrefix !== '') {
                // Create matching prefix/strings array entry for longest common prefix
                if (!array_key_exists($shortestCommonPrefix, $matched)) {
                    $matched[$shortestCommonPrefix] = [];
                }

                foreach ($input as $comparedString) {
                    // Add longest common prefix key strings without prefix
                    $stringWithoutPrefix = substr($comparedString, strlen($shortestCommonPrefix));
                    if (!in_array($stringWithoutPrefix, $matched[$shortestCommonPrefix], true) && is_string($stringWithoutPrefix)) {
                        $matched[$shortestCommonPrefix][] = $stringWithoutPrefix;
                    }
                }

                $matched[$shortestCommonPrefix] = $this->innerProcessor($matched[$shortestCommonPrefix]);
            }
        }

        return $matched;
    }
}
