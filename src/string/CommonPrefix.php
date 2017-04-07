<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 07.04.17 at 08:36
 */
namespace samsonframework\stringconditiontree\string;

/**
 * Class finds longest common prefix between strings,
 * considering fixed and variable character groups.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class CommonPrefix
{
    /** @var Structure Input string structure */
    protected $structure;

    /**
     * LongestMatchingPrefix constructor.
     *
     * @param string $string Input string
     */
    public function __construct(string $string)
    {
        $this->structure = new Structure($string);
    }

    /**
     * Get longest common prefix between strings.
     *
     * @param string $comparedString Compared string
     *
     * @return string Strings longest matching prefix or empty string
     */
    public function getCommonPrefix(string $comparedString)
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
            if (($prefix = $this->getGroupsCommonPrefix($initialGroup, $comparedGroup)) === $initialGroup->getString()) {
                break;
            }

            $longestPrefix .= $prefix;
        }

        return $longestPrefix;
    }

    /**
     * Get longest common prefix between character groups.
     *
     * @param AbstractCG $initial Initial character group
     * @param AbstractCG $compared Compared character group
     *
     * @return string Longest common prefix between groups
     */
    protected function getGroupsCommonPrefix(AbstractCG $initial, AbstractCG $compared): string
    {
        $prefix = '';

//        // We cannot
//        if ($initial->isSameType($compared) !== false) {
//            return $prefix;
//        }

        // Convert strings to arrays
        $initialArray = str_split($initial->getString());
        $comparedArray = str_split($compared->getString());

        // Get longest array
        $maxSize = max(count($initialArray), count($comparedArray));

        // Iterate longest array
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
