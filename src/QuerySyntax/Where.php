<?php

namespace PowerLink\QuerySyntax;


/**
 * Class Where.
 */
class Where
{
    const EQUAL = '=';
    const NOT_EQUAL = '!=';

    const GREATER_THAN = '>';
    const LESS_THAN = '<';
    const GREATER_THAN_OR_EQUAL = '>=';
    const LESS_THAN_OR_EQUAL = '<=';

    const AND = 'AND';
    const OR = 'OR';

    const START_WITH = 'start-with';
    const END_WITH = 'end-with';

    const NOT_START_WITH = 'not-start-with';
    const NOT_END_WITH = 'not-end-with';

    const IS_NULL = 'is-null';
    const IS_NOT_NULL = 'is-not-null';

    /**
     * @var array
     */
    protected $query;

    /**
     * Get names of types in the array
     * 
     * @param array $array Array 
     * @return string[] Array of types
     */
    private function getTypeNamesFromArray(array $array)
    {
        return array_map(function ($item) {
            return gettype($item);
        }, $array);
    }

    /**
     * Split query
     * 
     * @param array $array Array 
     * @return array Array of values and conjuctions
     */
    private function splitQuery(array $array)
    {
        $values = array();
        $conjuctions = array();
        $both = array(&$values, &$conjuctions);

        array_walk($array, function ($v, $k) use ($both) {
            $both[$k % 2][] = $v;
        });

        return $both;
    }

    /**
     * Validate where params
     * 
     * @param array $array Array for validation 
     * @return string[] array of types
     */
    private function validate(array $array)
    {
        if (in_array('array', $this->getTypeNamesFromArray($array))) {
            $splitted = $this->splitQuery($array);

            array_walk($splitted[1], function ($v) {
                if (!is_string($v)) {
                    throw new \InvalidArgumentException("Invalid conjuction");
                }
                if (!in_array($v, array(self::AND, self::OR))) {
                    throw new \InvalidArgumentException("Invalid conjuction $v");
                }
            });

            array_walk($splitted[0], function ($v) {
                if (!is_array($v)) {
                    throw new \InvalidArgumentException("Value is not an array");
                }
            });

            array_walk($splitted[0], function ($v) {
                $this->validate($v);
            });
        } elseif (count($array) !== 3) {
            if (!in_array($array[1], array(self::IS_NOT_NULL, self::IS_NOT_NULL))) {
                throw new \InvalidArgumentException("Invalid length of query params");
            }
        } elseif (
            count($array) === 3 &&
            !in_array(
                $array[1],
                array(
                    self::EQUAL, self::NOT_END_WITH,
                    self::GREATER_THAN, self::GREATER_THAN_OR_EQUAL,
                    self::LESS_THAN, self::LESS_THAN_OR_EQUAL,
                    self::START_WITH, self::NOT_START_WITH,
                    self::END_WITH, self::NOT_END_WITH,
                )
            )
        ) {
            throw new \InvalidArgumentException("Invalid operator $array[1]");
        }
    }

    /**
     * @param array $query
     */
    public function __construct(array $query)
    {
        $this->validate($query);
        $this->setQuery($query);
    }

    /**
     * @param array $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Get builded query
     * @return string
     */
    public function getQuery()
    {
        return $this->buildQuery($this->query);
    }

    /**
     * @param array $statement
     */
    private function joinAndWrapInBrackets($statement)
    {
        return '(' . implode(' ', $statement) . ')';
    }

    /**
     * Build query
     * @return string
     */
    public function buildQuery($query)
    {
        $builtQuery = array();

        if (in_array('array', $this->getTypeNamesFromArray($query))) {
            $splitted = $this->splitQuery($query);

            while (!empty($splitted[0]) || !empty($splitted[1])) {
                if (!empty($splitted[0])) {
                    $value = array_shift($splitted[0]);
                    $builtQuery[] = $this->buildQuery($value);
                }

                if (!empty($splitted[1])) {
                    $builtQuery[] = array_shift($splitted[1]);
                }
            }
        } else {
            $builtQuery = $query;
        }

        return $this->joinAndWrapInBrackets($builtQuery);
    }
}
