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
                        $foundPrefix = $this->findPrefixInExistingCommonPrefix($foundPrefix, $commonPrefixes);

                        $this->addToCommonPrefixesCollection(
                            $commonPrefixes,
                            $foundPrefix,
                            $comparedStructure,
                            $usedStructures
                        );

                        $oneCommonPrefixFound = true;
                    }
                }
            }

            if (!$oneCommonPrefixFound) {
                $this->addToCommonPrefixesCollection(
                    $commonPrefixes,
                    $initialStructure->getString(),
                    $initialStructure,
                    $usedStructures
                );
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
     */
    protected function sort(bool $ascending = true): void
    {
        // Sort internalCollection
        uasort($this->structures, function (Structure $initial, Structure $compared) {
            return $initial->compare($compared);
        });

        // Sort descending if needed
        $this->structures = $ascending ? array_reverse($this->structures) : $this->structures;
    }

    private function findPrefixInExistingCommonPrefix(string $prefix, array $existingPrefixes): string
    {
        /**
         * Try to find if this prefix can be merged into already found common prefix
         * as our structures collection is already sorted.
         */
        $foundPrefixStructure = new Structure($prefix);
        foreach ($existingPrefixes as $existingPrefix => $structures) {
            $internalPrefix = (new Structure($existingPrefix))->getCommonPrefix($foundPrefixStructure);
            if ($internalPrefix !== '') {
                return $internalPrefix;
            }
        }

        return $prefix;
    }

    private function addToCommonPrefixesCollection(
        array &$commonPrefixes,
        string $foundPrefix,
        Structure $comparedStructure,
        array &$usedStructures
    ): void {
        // Create new structure collection with common prefix
        if (!array_key_exists($foundPrefix, $commonPrefixes)) {
            $commonPrefixes[$foundPrefix] = new StructureCollection();
        }

        $newPrefix = substr($comparedStructure->getString(), strlen($foundPrefix));
        if ($newPrefix !== '' && !in_array($comparedStructure, $usedStructures, false)) {
            $usedStructures[] = $comparedStructure;
            // Add structure to structure collection
            $commonPrefixes[$foundPrefix]->add(new Structure($newPrefix));
        }
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

    /**
     * @param Structure $structure
     *
     * @return bool
     */
    public function has(Structure $structure): bool
    {
        return in_array($structure, $this->structures);
    }
}
