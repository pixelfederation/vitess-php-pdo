<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;

use VitessPdo\PDO\Dsn\Dsn as DsnConfig;

/**
 * Description of trait Vctld
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\DependencyTrait
 */
trait Dsn
{

    /**
     * @var DsnConfig
     */
    protected $dsn;

    /**
     * QueryUse constructor.
     *
     * @param DsnConfig $dsn
     */
    private function setDsn(DsnConfig $dsn)
    {
        $this->dsn = $dsn;
    }
}
