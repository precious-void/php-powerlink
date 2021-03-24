<?php

namespace PowerLink\Model;

/**
 * Class defining a query request in PowerLink with methods to set
 * query settings
 */
class QueryModel
{
    public const ASC = 'asc';
    public const DESC = 'DESC';

    /**
     * Object Type 
     * @var string
     */
    public $object_type;

    /**
     * Number of items returned per page 
     * @var string
     */
    public $page_size;

    /**
     * Number of requested page 
     * @var string
     */
    public $page_number;

    /**
     * Array of requested fields 
     * @var array
     */
    public $fields;

    /**
     * SQL query for selecting ites 
     * @var string
     */
    public $query;

    /**
     * Name of field to sort by 
     * @var string
     */
    public $sort_by;

    /**
     * Name of field to sort by 
     * @var ASC|DESC
     */
    public $sort_type;
}
