<?php

namespace PowerLink\Query\Syntax;


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
}
