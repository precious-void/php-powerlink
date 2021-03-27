<?php

namespace PowerLink\QuerySyntax;

/**
 * Class OrderBy.
 */
class OrderBy
{
    const ASC = 'asc';
    const DESC = 'desc';

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

    /**
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $sort_by, $sort_type = self::ASC)
    {
        $this->setField($sort_by);
        $this->setDirection($sort_type);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setDirection($direction)
    {
        if (!in_array($direction, array(self::ASC, self::DESC))) {
            throw new \InvalidArgumentException(
                "Specified direction '$direction' is not allowed. Only ASC or DESC are allowed."
            );
        }

        $this->direction = $direction;

        return $this;
    }
}
