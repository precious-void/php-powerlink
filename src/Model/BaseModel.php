<?php

namespace PowerLink\Model;

abstract class BaseModel
{
    /**
     * Object type
     * @var string 
     */
    protected $object_type;

    /**
     * @param string $object_type Object Type
     */
    public function __construct(string $object_type)
    {
        $this->object_type = $object_type;
    }
}
