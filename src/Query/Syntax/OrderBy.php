<?php

namespace PowerLink\Query\Syntax;

/**
 * Class OrderBy.
 */
class OrderBy
{
    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var bool
     */
    protected $useAlias;

    /**
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $field, $direction)
    {
        $this->setField($field);
        $this->setDirection($direction);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field $field
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
