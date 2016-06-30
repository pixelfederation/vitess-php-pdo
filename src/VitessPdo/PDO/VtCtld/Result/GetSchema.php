<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Result\GetSchema\Schema;

/**
 * Description of class GetSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class GetSchema extends Result
{

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $data = json_decode(trim($this->responseString), true);

        $this->data = new Schema($data);
    }
}
