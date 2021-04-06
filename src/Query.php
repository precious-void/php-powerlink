<?php

namespace PowerLink;

use PowerLink\QuerySyntax\OrderBy;
use PowerLink\QuerySyntax\Where;
use PowerLink\Exceptions\QueryException;

/**
 * Class defining a query request in PowerLink with methods to set
 * query settings
 */
class Query
{
    /**
     * SQL query for selecting ites 
     * @var string
     */
    protected $query = '';

    /**
     * Object Type 
     * @var string
     */
    protected $object_type;

    /**
     * Number of items returned per page 
     * @var int
     */
    protected $page_size = 50;

    /**
     * Number of requested page 
     * @var int
     */
    protected $page_number = 1;

    /**
     * Array of requested fields 
     * @var array|null
     */
    protected $fields = null;

    /**
     * Order By object 
     * @var OrderBy
     */
    protected $order_by;

    /**
     * Set object type
     * @param int $object_type Object Type
     */
    public function setObjectType(int $object_type)
    {
        $this->object_type = $object_type;
    }

    /**
     * Set object type
     * @param string $field Field name
     * @param OrderBy::ASC|OrderBy::DESC|string $sort_by Field name
     */
    public function setOrderBy(string $field, string $sort_by)
    {
        $this->order_by = new OrderBy($field, $sort_by);
    }

    /**
     * Set fields to request
     * @param array|string $field Field name
     * @throws \InvalidArgumentException Exception if you specified fields in wrong format
     */
    public function setFields($fields)
    {
        if (is_array($fields)) {
            if (empty($fields)) {
                throw new \InvalidArgumentException('You cannot specify empty field names');
            }
            $this->fields = $fields;
        } elseif (is_string($fields) && $fields === '*') {
            $this->fields = null;
        } else {
            throw new \InvalidArgumentException('You can specify fields by passing an array of field names');
        }
    }

    /**
     * Set page size
     * @param int $page_size Page size
     */
    public function setPageSize(int $page_size)
    {
        if (is_int($page_size)) {
            throw new \InvalidArgumentException('Page number should be integer');
        }

        $this->page_size = $page_size;
    }

    /**
     * Set page number
     * @param int $page_number Page number
     */
    public function setPageOffset(int $page_number)
    {
        if (is_int($page_number)) {
            throw new \InvalidArgumentException('Page number should be integer');
        }

        $this->page_number = $page_number;
    }

    /**
     * Set query
     * @param int $query Query
     */
    public function setQuery(array $query)
    {
        $queryBuilder = new Where($query);
        $this->query = $queryBuilder->getQuery();
    }

    public function getParams()
    {
        if (!empty($this->object_type)) throw new QueryException('Object type should be specified');

        $params = array(
            'objecttype'    => $this->object_type,
            'fields'        => is_array($this->fields) ? implode(',', $this->fields) : '*',
            'page_number'   => $this->page_number,
            'page_size'     => $this->page_size,
        );

        if ($this->order_by instanceof OrderBy) {
            $params['sort_by'] = $this->order_by->getField();
            $params['sort_type'] = $this->order_by->getDirection();
        }

        if (!empty($this->query)) {
            $params['query'] = $this->query;
        }

        return $params;
    }
}
