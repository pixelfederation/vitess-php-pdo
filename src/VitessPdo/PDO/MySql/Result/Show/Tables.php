<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Result\Show;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\VtCtld\Result\GetVSchema;

/**
 * Description of class Tables
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class Tables extends VtCtldResult
{

    /**
     * Databases constructor.
     *
     * @param GetVSchema $result
     */
    public function __construct(GetVSchema $result)
    {
        $this->initFields($result->getKeyspace());
        parent::__construct($result);
    }

    /**
     * @param string $keyspace
     * @return void
     */
    private function initFields($keyspace)
    {
        $this->specializedFIelds = [
            "Tables_in_{$keyspace}", 0,
        ];
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function transform($data)
    {
        if (!isset($data['tables'])) {
            throw new Exception('Missing vschema data key - tables.');
        }

        $field = $this->specializedFIelds[0];
        $returnData = [];

        foreach ($data['tables'] as $table => $config) {
            $returnData[] = [
                $field => $table,
                0 => $table,
            ];
        }

        return $returnData;
    }
}
