<?php

namespace PowerLink\Query\Syntax;


/**
 * Class Where.
 */
class Where
{
    const OPERATOR_EQUAL = '=';
    const OPERATOR_NOT_EQUAL = '!=';

    const OPERATOR_GREATER_THAN = '>';
    const OPERATOR_LESS_THAN = '<';
    const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_LESS_THAN_OR_EQUAL = '<=';

    const CONJUNCTION_AND = 'AND';
    const CONJUNCTION_OR = 'OR';

    const OPERATOR_START_WITH = 'start-with';
    const OPERATOR_END_WITH = 'end-with';

    const OPERATOR_NOT_START_WITH = 'not-start-with';
    const OPERATOR_NOT_END_WITH = 'not-end-with';

    const OPERATOR_IS_NULL = 'is-null';
    const OPERATOR_IS_NOT_NULL = 'is-not-null';

    /**
     * Add a basic where clause to the query.
     *
     * @param  string $field
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($field, $operator = null, $value = null, $boolean = 'and')
    {
        // If the field is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($field)) {
            return $this->addArrayOfWheres($field, $boolean);
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2
        );

        // If the fields is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parenthesis.
        // We'll add that Closure to the query then return back out immediately.
        if ($field instanceof Closure && is_null($operator)) {
            return $this->whereNested($field, $boolean);
        }

        // If the field is a Closure instance and there is an operator value, we will
        // assume the developer wants to run a subquery and then compare the result
        // of that subquery with the given value that was provided to the method.
        if ($this->isQueryable($field) && !is_null($operator)) {
            [$sub, $bindings] = $this->createSub($field);

            return $this->addBinding($bindings, 'where')
                ->where(new Expression('(' . $sub . ')'), $operator, $value, $boolean);
        }

        // If the given operator is not found in the list of valid operators we will
        // assume that the developer is just short-cutting the '=' operators and
        // we will set the operators to '=' and set the values appropriately.
        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        // If the value is a Closure, it means the developer is performing an entire
        // sub-select within the query and we will need to compile the sub-select
        // within the where clause to get the appropriate query record results.
        if ($value instanceof Closure) {
            return $this->whereSub($field, $operator, $value, $boolean);
        }

        // If the value is "null", we will just assume the developer wants to add a
        // where null clause to the query. So, we will allow a short-cut here to
        // that method for convenience so the developer doesn't have to check.
        if (is_null($value)) {
            return $this->whereNull($field, $boolean, $operator !== '=');
        }

        $type = 'Basic';

        // If the field is making a JSON reference we'll check to see if the value
        // is a boolean. If it is, we'll add the raw boolean string as an actual
        // value to the query to ensure this is properly handled by the query.
        if (Str::contains($field, '->') && is_bool($value)) {
            $value = new Expression($value ? 'true' : 'false');

            if (is_string($field)) {
                $type = 'JsonBoolean';
            }
        }

        // Now that we are working with just a simple query we can put the elements
        // in our array and add the query binding to our array of bindings that
        // will be bound to each SQL statements when it is finally executed.
        $this->wheres[] = compact(
            'type',
            'field',
            'operator',
            'value',
            'boolean'
        );

        if (!$value instanceof Expression) {
            $this->addBinding($this->flattenValue($value), 'where');
        }

        return $this;
    }
}
