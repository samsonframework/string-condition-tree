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
    /** string Internal collection name */
    protected const COLLECTION_NAME = 'structures';

    /** @var Structure[] */
    protected $structures = [];

    /**
     * Create structures collection from array of strings.
     *
     * @param array $strings Strings array
     *
     * @return StructureCollection StructureCollection instance
     *
     * @throws \InvalidArgumentException If collection variable is missing
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
     * @return StructureCollection[] Longest common prefixes array of StructureCollection
     */
    public function getCommonPrefixesCollection(): array
    {
        $this->sort();

        /** @var StructureCollection[] $commonPrefixes */
        $commonPrefixes = [];

        /** @var StructureCollection $usedStructures */
        $usedStructures = new StructureCollection();

        // Iterate sorted character group internalCollection
        foreach ($this->structures as $initialStructure) {
            $oneCommonPrefixFound = false;
            // Iterate all character group internalCollection again
            foreach ($this->structures as $comparedStructure) {
                // Ignore same internalCollection
                if ($initialStructure !== $comparedStructure) {
                    $foundPrefix = $initialStructure->getCommonPrefix($comparedStructure);

                    // If we have found common prefix between two structures
                    if ($foundPrefix !== '') {
                        /**
                         * Try to find if this prefix can be merged into already found common prefix
                         * as our structures collection is already sorted.
                         */
                        if (strpos($foundPrefix, '{z}') !== false) {
                            var_dump(1);
                        }
                        $foundPrefixStructure = new Structure($foundPrefix);
                        foreach ($commonPrefixes as $existingPrefix => $structures) {
                            $internalPrefix = (new Structure($existingPrefix))->getCommonPrefix($foundPrefixStructure);
                            if ($internalPrefix !== '') {
                                $foundPrefix = $internalPrefix;
                                break;
                            }
                        }

                        // Create new structure collection with common prefix
                        if (!array_key_exists($foundPrefix, $commonPrefixes)) {
                            $commonPrefixes[$foundPrefix] = new StructureCollection();
                        }

                        // Add structure to structure collection
                        $usedStructures->addStructure($comparedStructure);
                        $commonPrefixes[$foundPrefix]->addStructure($comparedStructure);
                        $oneCommonPrefixFound = true;
                    }
                }
            }

            if (!$oneCommonPrefixFound) {
                $foundPrefix = $initialStructure->getString();

                // Create new structure collection with common prefix
                if (!array_key_exists($foundPrefix, $commonPrefixes)) {
                    $commonPrefixes[$foundPrefix] = new StructureCollection();
                }

                // Add structure to structure collection
                $commonPrefixes[$foundPrefix]->addStructure($initialStructure);
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
     * Add structure to structure collection.
     *
     * @param Structure $structure Added structure
     */
    public function addStructure(Structure $structure)
    {
        // Search for existing structure
        $found = false;
        foreach ($this->structures as $comparedStructure) {
            if ($this->isSameStructure($structure, $comparedStructure)) {
                $found = true;
            }
        }

        if (!$found) {
            $this->structures[$structure->getString()] = $structure;
        }
    }

    /**
     * Compare two structures.
     *
     * @param Structure $initial
     * @param Structure $compared
     *
     * @return bool
     */
    protected function isSameStructure(Structure $initial, Structure $compared): bool
    {
        return $initial->getString() === $compared->getString();
    }

    /**
     * @param Structure $structure
     *
     * @return bool
     */
    public function has(Structure $structure): bool
    {
        foreach ($this->structures as $comparedStructure) {
            if ($this->isSameStructure($structure, $comparedStructure)) {
                return true;
            }
        }

        return false;
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
}
