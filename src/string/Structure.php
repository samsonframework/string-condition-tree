<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 06.04.17 at 07:30
 */
namespace samsonframework\stringconditiontree\string;

/**
 * This class describes string character group structure(CGS).
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class Structure
{
    /** string Character group matching pattern */
    const PATTERN = '/'.FixedCharacterGroup::PATTERN.'|'.VariableCharacterGroup::PATTERN.'/';

    /** @var AbstractCharacterGroup[] */
    public $groups = [];

    /** @var string Input string */
    public $input;

    /**
     * Create string character group structure from input string.
     *
     * @param string $input Input string for string character group structure
     */
    public function __construct(string $input)
    {
        $this->input = $input;

        // Iterate input and find fixed/variable groups
        while (preg_match(self::PATTERN, $input, $matches)) {
            $input = str_replace($matches[0], '', $input);
            if (array_key_exists(VariableCharacterGroup::PATTERN_GROUP, $matches)) {
                $this->groups[] = new VariableCharacterGroup(strlen($matches[VariableCharacterGroup::PATTERN_GROUP]));
            } else {
                $this->groups[] = new FixedCharacterGroup(strlen($matches[FixedCharacterGroup::PATTERN_GROUP]));
            }
        }
    }

    /**
     * Compare structure character groups.
     *
     * @param Structure $structure Compared string structure group
     *
     * @return int Comparison result
     */
    public function compare(Structure $structure): int
    {
        $comparedStructureSize = count($structure->groups);
        foreach ($this->groups as $index => $group) {
            // Get compared group or last compared character group is size mismatches
            $comparedGroup = $structure[$index] ?? $structure[$comparedStructureSize-1];

            if (($return = $group->compare($comparedGroup)) !== 0) {
                return $return;
            }
        }
    }
}
