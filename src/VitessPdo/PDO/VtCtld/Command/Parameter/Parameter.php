<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command\Parameter;

/**
 * Description of class Parameter
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command\Parameter
 */
class Parameter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * NamedParameter constructor.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $this->sanitize($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function sanitize($value)
    {
        $value = escapeshellarg(trim((string) $value));
        $value = str_replace("\n", ' ', $value);

        return $value;
    }
}
