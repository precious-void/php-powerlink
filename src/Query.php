<?php

namespace PowerLink;

/**
 * Class defining a query request in PowerLink with methods to set
 * query settings
 */
class Query
{
    /**
     * Object Type 
     * @var string
     */
    protected $object_type;

    /**
     * Number of items returned per page 
     * @var string
     */
    protected $page_size;

    /**
     * Number of requested page 
     * @var string
     */
    protected $page_number;

    /**
     * Array of requested fields 
     * @var array
     */
    protected $fields;

    /**
     * SQL query for selecting ites 
     * @var string
     */
    protected $query;

    /**
     * Name of field to sort by 
     * @var string
     */
    protected $sort_by;

    /**
     * Name of field to sort by 
     * @var ASC|DESC
     */
    protected $sort_type;
}
