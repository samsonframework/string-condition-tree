<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 07.04.17 at 08:36
 */
namespace samsonframework\stringconditiontree\string;

/**
 * Class LongestMatchingPrefix
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class LongestMatchingPrefix
{
    /** @var string Input string */
    protected $string;

    /** @var Structure Input string structure */
    protected $structure;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->structure = new Structure($string);
    }

    public function get(string $comparedString)
    {
        $compared = new Structure($comparedString);

        $longestPrefix = '';

        // Define shortest structure
        $shortestStructure = $this->structure->count() < $compared->count() ? $this->structure : $compared;

        /** @var AbstractCG $group Iterate longest structure character groups */
        foreach ($shortestStructure as $index => $group) {
            $initialGroup = $this->structure->groups[$index];
            $comparedGroup = $compared->groups[$index];

            /**
             * Get longest matching prefix between character groups.
             * If returned prefix is not equal initial character groups then it means
             * that it is shorter and we need to stop searching.
             */
            if (($prefix = $this->compareGroups($initialGroup, $comparedGroup)) === $initialGroup->getString()) {
                break;
            }

            $longestPrefix .= $prefix;
        }

        return $longestPrefix;
    }

    protected function compareGroups(AbstractCG $initial, AbstractCG $compared): string
    {
        // Convert strings to arrays
        $initialArray = str_split($initial->getString());
        $comparedArray = str_split($compared->getString());

        // Get longest array
        $maxSize = max(count($initialArray), count($comparedArray));

        // Iterate longest array
        $prefix = '';
        for ($i = 0; $i < $maxSize; $i++) {
            // Get existing character or empty string
            $initialChar = $initialArray[$i] ?? '';
            $comparedChar = $comparedArray[$i] ?? '';

            // On first mismatch - break
            if ($initialChar !== $comparedChar) {
                break;
            }

            $prefix .= $initialChar;
        }

        return $prefix;
    }
}
