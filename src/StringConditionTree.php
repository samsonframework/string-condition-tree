<?php declare(strict_types = 1);
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 02.03.17 at 13:25
 */
namespace samsonframework\stringconditiontree;

use samsonframework\stringconditiontree\string\StructureCollection;

/**
 * Class StringConditionTree
 *
 * TODO: Remove variable character group markers, move LMP search to string.
 *
 * @author Vitaly Egorov <egorov@samsonos.com>
 */
class StringConditionTree
{
    /** Tree node root element identifier, needed for recursion */
    const ROOT_NAME = '';

    /** Final tree node branch identifier */
    const SELF_NAME = '@self';

    /** String parameter start marker */
    const PARAMETER_START = '{';

    /** String parameter end marker */
    const PARAMETER_END = '}';

    /** Parameter sorting length value for counting */
    const PARAMETER_COF = 2000;

    /** @var TreeNode Resulting internalCollection for debugging */
    protected $debug;

    /** @var array Collection of string string => identifier */
    protected $source;

    /** @var string Parametrized string start marker */
    protected $parameterStartMarker = self::PARAMETER_START;

    /** @var string Parametrized string end marker */
    protected $parameterEndMarker = self::PARAMETER_END;

    /**
     * StringConditionTree constructor.
     *
     * @param string               $parameterStartMarker Parametrized string start marker
     * @param string               $parameterEndMarker   Parametrized string end marker
     */
    public function __construct(
        string $parameterStartMarker = self::PARAMETER_START,
        string $parameterEndMarker = self::PARAMETER_END
    ) {
        $this->parameterStartMarker = $parameterStartMarker;
        $this->parameterEndMarker = $parameterEndMarker;
    }

    /**
     * Build similarity strings tree.
     *
     * @param array $input Collection of strings
     *
     * @return TreeNode Resulting similarity strings tree
     */
    public function process(array $input): TreeNode
    {
        $this->source = $input;

        $this->processor(
            StructureCollection::fromStringsArray(array_keys($input))->getCommonPrefixesCollection(),
            $this->debug = new TreeNode()
        );

        return $this->debug;
    }

    /**
     * @param StructureCollection[] $collection
     */
    protected function processor(array $collection, TreeNode $parent, string $parentPrefix = ''): void
    {
        foreach ($collection as $prefix => $item) {
            // Create tree node. Pass string identifier if present
            $newChild = $parent->append($prefix, $this->source[$parentPrefix.$prefix] ?? '');

            $lcpCollection = $item->getCommonPrefixesCollection();

            $this->processor($lcpCollection, $newChild, $parentPrefix.$prefix);
        }
    }
}
