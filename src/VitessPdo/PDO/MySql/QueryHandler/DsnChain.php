<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\Dsn\Dsn;

/**
 * Description of class DsnChain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class DsnChain extends Chain
{

    use DependencyTrait\Dsn;

    /**
     * VctldMember constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->setDsn($dsn);
        parent::__construct();
    }
}
