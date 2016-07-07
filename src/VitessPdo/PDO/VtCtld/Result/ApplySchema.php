<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;

/**
 * Description of class ApplySchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class ApplySchema extends Result
{

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $data = json_decode(trim($this->responseString), true);

        $this->data = $data;
    }
}
