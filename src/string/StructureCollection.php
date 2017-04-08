<?php declare(strict_types=1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 08.04.17 at 09:08
 */
namespace samsonframework\stringconditiontree\string;

use samsonframework\stringconditiontree\AbstractIterable;

/**
 * Class StructureCollection
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StructureCollection extends AbstractIterable
{
    protected const COLLECTION_NAME = 'structures';
    /** @var Structure[] */
    protected $structures = [];

    /**
     * Create structures collection from array of strings.
     *
     * @param array $strings Strings array
     *
     * @return StructureCollection StructureCollection instance
     */
    public static function fromStringsArray(array $strings): StructureCollection
    {
        // Create internalCollection
        $structureCollection = new StructureCollection();
        foreach ($strings as $string) {
            $structureCollection->structures[$string] = new Structure($string);
        }

        return $structureCollection;
    }

    /**
     * Get internalCollection of grouped longest matching prefixes with strings sub-array.
     *
     * @return array Longest matching prefixes array
     */
    public function getCommonPrefixesCollection(): array
    {
        $this->sort();

        $commonPrefixes = [];

        $commonPrefixesCollection = new StructureCollection();

        // Iterate sorted character group internalCollection
        foreach ($this->structures as $initialStructure) {
            // Iterate all character group internalCollection again
            foreach ($this->structures as $comparedStructure) {
                // Ignore same internalCollection
                if ($initialStructure !== $comparedStructure) {
                    $foundPrefix = $initialStructure->getCommonPrefix($comparedStructure);

                    $commonPrefixesCollection->addString($foundPrefix);

                    // If we have found common prefix between two structures
                    if ($foundPrefix !== '') {
                        /**
                         * Try to find if this prefix can be merged into already found common prefix
                         * as our structures collection is already sorted.
                         */
                        $foundPrefixStructure = new Structure($foundPrefix);
                        foreach ($commonPrefixes as $existingPrefix) {
                            if ($foundPrefixStructure) {

                            }
                        }
                    }

                    $commonPrefixes[$foundPrefix][] = $comparedStructure;
                }
            }
        }

        return $commonPrefixes;
    }

    /**
     * Sort structures.
     *
     * @param bool $ascending Ascending sorting order
     *
     * @return array|Structure|Structure[]
     */
    protected function sort(bool $ascending = true)
    {
        // Sort internalCollection
        uasort($this->structures, function (Structure $initial, Structure $compared) {
            return $initial->compare($compared);
        });

        // Sort descending if needed
        $this->structures = $ascending ? array_reverse($this->structures) : $this->structures;
    }

    /**
     * Add string to structure collection.
     *
     * @param string $string Input string
     */
    public function addString(string $string)
    {
        $this->addStructure(new Structure($string));
    }

    /**
     * Add structure to structure collection.
     *
     * @param Structure $structure Added structure
     */
    public function addStructure(Structure $structure)
    {
        $this->structures[$structure->getString()] = $structure;
    }
}
