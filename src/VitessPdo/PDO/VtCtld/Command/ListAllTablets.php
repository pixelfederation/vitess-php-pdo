<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;

/**
 * Description of class GetVSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
class ListAllTablets extends DsnDependent
{

    /**
     * @const string
     */
    const PARAM_CELL = 'cell';

    /**
     * @param Dsn $dsn
     *
     * @return DsnDependent
     * @throws Exception
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;
        $cell = $dsn->getConfig()->getCell();

        if (!$cell) {
            throw new Exception("Cell missing.");
        }

        $this->set(self::PARAM_CELL, $cell);

        return $this;
    }
}
