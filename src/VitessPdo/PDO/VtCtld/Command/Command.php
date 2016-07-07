<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Parameter\NamedParameter;

/**
 * Description of class Command
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
abstract class Command
{

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->name = array_pop(explode('\\', get_called_class()));
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getResultClass()
    {
        return str_replace('Command', 'Result', __NAMESPACE__) . '\\' . $this->getName();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parts = [get_called_class()];

        foreach ($this->getParams() as $param) {
            $parts[] = (string) $param;
        }

        return implode('|', $parts);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return Command
     */
    protected function set($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @throws Exception
     */
    protected function get($key)
    {
        if (!isset($this->params[$key])) {
            throw new Exception("Key not found - {$key}.");
        }
    }
}
