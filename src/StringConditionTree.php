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
        $result = [];
        $result = $this->innerProcessor($input[0], $input);
        return $result;
    }

    protected function innerProcessor(string $sourceKey, array $input)
    {
        $matched = [];
        $missed = [];

        $matchedStr = '';

        $stringLength  = strlen($sourceKey);

        for ($i = 0; $i < $stringLength; $i++) {
            $char = $sourceKey{$i};

            foreach ($input as $key) {
                if (!isset($key{$i}) || $key{$i} !== $sourceKey{$i}) {
                    break 2;
                }
            }
            $matchedStr .= $char;
        }

        foreach ($input as $key) {
            if (strpos($key, $matchedStr) === 0) {
                $matched[] = substr($key, $i, strlen($key));
            } else {
                $missed[] = $key;
            }
        }

        return [$matchedStr => $matched, $missed];
    }
}
