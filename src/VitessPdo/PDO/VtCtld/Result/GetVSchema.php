<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;

/**
 * Description of class GetVSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class GetVSchema extends Result
{

    /**
     * @return string
     */
    public function getKeyspace()
    {
        return $this->dsn->getConfig()->getKeyspace();
    }

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $this->data = json_decode(trim($this->responseString), true);
    }
}
