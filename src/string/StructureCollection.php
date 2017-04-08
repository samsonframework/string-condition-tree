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
     * Get collection of StructureCollection instances grouped by longest common prefixes.
     *
     * @return StructureCollection[] Longest common prefixes array of StructureCollection instances
     */
    public function getCommonPrefixesCollection(): array
    {
        $this->sort();

        /** @var StructureCollection[] $commonPrefixes */
        $commonPrefixes = [];

        /** @var Structure[] $usedStructures */
        $usedStructures = [];

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

                        $foundInOtherCollection = in_array($comparedStructure, $usedStructures);

                        if (strpos($foundPrefix,'{z}') !== false ) {
                            var_dump(1);
                        }

                        $newPrefix = substr($comparedStructure->getString(), strlen($foundPrefix));
                        if (!$foundInOtherCollection && strlen($newPrefix)) {
                            $usedStructures[] = $comparedStructure;
                            // Add structure to structure collection
                            $commonPrefixes[$foundPrefix]->addUniqueStructure(new Structure($newPrefix));
                        }

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

                $foundInOtherCollection = in_array($initialStructure, $usedStructures);

                $newPrefix = substr($initialStructure->getString(), strlen($foundPrefix));
                if (!$foundInOtherCollection && strlen($newPrefix)) {
                    $usedStructures[] = $initialStructure;
                    // Add structure to structure collection
                    $commonPrefixes[$foundPrefix]->addUniqueStructure(new Structure($newPrefix));
                }
            }
        }

        // Sort common prefixes
        $commonPrefixesCollection = new StructureCollection();
        foreach ($commonPrefixes as $prefix => $structures) {
            $commonPrefixesCollection->add(new Structure($prefix));
        }
        $commonPrefixesCollection->sort();

        $final = [];
        foreach ($commonPrefixesCollection as $prefix => $structureCollection) {
            $final[$prefix] = $commonPrefixes[$prefix];
        }

        return $final;
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
     * Add only unique structure to collection.
     *
     * @param Structure $structure Added structure
     */
    public function addUniqueStructure(Structure $structure): void
    {
        if (!$this->has($structure)) {
            $this->add($structure);
        }
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
     * Compare two structures.
     *
     * @param Structure $initial Initial structure
     * @param Structure $compared Compared structure
     *
     * @return bool True is structures are equal
     */
    protected function isSameStructure(Structure $initial, Structure $compared): bool
    {
        return $initial->getString() === $compared->getString();
    }

    /**
     * Add structure to structure collection.
     *
     * @param Structure $structure Added structure
     */
    public function add(Structure $structure): void
    {
        $this->structures[$structure->getString()] = $structure;
    }
}
