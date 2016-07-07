<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Parameter\NamedParameter;
use VitessPdo\PDO\VtCtld\Command\Parameter\Parameter;

/**
 * Description of class ApplySchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
class ApplySchema extends DsnDependent
{

    /**
     * @const string
     */
    const PARAM_KEYSPACE = 'keyspace';

    /**
     * @const string
     */
    const PARAM_DDL_SQL = 'sql';

    /**
     * GetSchema constructor.
     *
     * @param string $sql
     */
    public function __construct($sql)
    {
        $this->set(self::PARAM_DDL_SQL, new NamedParameter(self::PARAM_DDL_SQL, $sql));
    }

    /**
     * @param Dsn $dsn
     *
     * @return DsnDependent
     * @throws Exception
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;
        $keyspace = $dsn->getConfig()->getKeyspace();

        if (!$keyspace) {
            throw new Exception("SQLSTATE[3D000]: Invalid catalog name: 1046 No database selected");
        }

        $this->set(self::PARAM_KEYSPACE, new Parameter(self::PARAM_KEYSPACE, $keyspace));

        return $this;
    }
}
