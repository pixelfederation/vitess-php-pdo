<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command\Parameter;

/**
 * Description of class NamedParameter
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command\Parameter
 */
class NamedParameter extends Parameter
{

    /**
     * @return string
     */
    public function __toString()
    {
        return "-{$this->name}={$this->value}";
    }
}
