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

    public function process(array $input) {
        return $this->innerProcessor($input[0], $input);
    }

    protected function innerProcessor(string $sourceKey, array $input)
    {
        $matched = [];
        $missed = [];

        // Gather matched prefix for all input strings
        $matchedPrefix = '';

        // Iterate characters in source key
        for ($i = 0, $stringLength  = strlen($sourceKey); $i < $stringLength; $i++) {
            // Get i character from source key
            $char = $sourceKey{$i};

            // Iterate all input strings
            foreach ($input as $inputKey) {
                /**
                 * Check is input string has i character and
                 * if it does not match i source key character.
                 */
                if (!isset($inputKey{$i}) || $inputKey{$i} !== $char) {
                    // Break parent for loop as characters mismatch;
                    break 2;
                }
            }

            /** Collected matched prefix from source key */
            $matchedPrefix .= $char;
        }

        foreach ($input as $inputKey) {
            if (strpos($inputKey, $matchedPrefix) === 0) {
                $matched[] = substr($inputKey, $i, strlen($inputKey));
            } else {
                $missed[] = $inputKey;
            }
        }

        return [$matchedPrefix => $matched, $missed];
    }
}
