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
     * GetVSchema constructor.
     *
     * @param $keyspace
     * @param $responseString
     */
    public function __construct($keyspace, $responseString)
    {
        $this->parse($keyspace, $responseString);
    }

    /**
     * @param string $keyspace
     * @param string $responseString
     *
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function parse($keyspace, $responseString)
    {
        $field = "Tables_in_{$keyspace}";
        $data = json_decode(trim($responseString), true);

        if (!isset($data['tables'])) {
            throw new Exception('Missing vschema data key - tables.');
        }

        foreach ($data['tables'] as $table => $config) {
            $this->data[] = [
                $field => $table,
                0 => $table,
            ];
        }

        $this->fields = [$field];
    }
}
