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
    /** @var Structure[] */
    protected $structures = [];

    /**
     * StructureCollection constructor.
     *
     * @throws \InvalidArgumentException If collection variable is missing
     */
    public function __construct()
    {
        parent::__construct('structures');
    }

    /**
     * Create structures collection from array of strings.
     *
     * @param array $strings Strings array
     *
     * @return StructureCollection StructureCollection instance
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
}
