<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\Dsn\Dsn;

/**
 * Description of class DsnMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class DsnMember extends Member
{

    use DependencyTrait\Dsn;

    /**
     * DsnMember constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->setDsn($dsn);
    }
}
