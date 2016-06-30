<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Dsn\Dsn;

/**
 * Description of class DsnDependent
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
abstract class DsnDependent extends Command
{

    /**
     * @var Dsn
     */
    protected $dsn;

    /**
     * @param Dsn $dsn
     *
     * @return DsnDependent
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;

        return $this;
    }
}
