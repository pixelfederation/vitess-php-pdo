<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;

/**
 * Description of class GetKeyspaces
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
class GetKeyspaces extends Result
{

    /**
     * GetVSchema constructor.
     *
     * @param $responseString
     */
    public function __construct($responseString)
    {
        $this->fields = [
            'Database',
            0
        ];

        $this->parse($responseString);
    }

    /**
     * @param string $responseString
     *
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function parse($responseString)
    {
        $keyspaces = explode("\n", trim($responseString));

        $this->data = array_map(function ($keyspace) {
            return [
                'Database' => $keyspace,
                0 => $keyspace,
            ];
        }, $keyspaces);
    }
}
