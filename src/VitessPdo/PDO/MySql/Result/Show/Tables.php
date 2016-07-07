<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Result\Show;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

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
     * @param GetSchema $result
     */
    public function __construct(GetSchema $result)
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
     * @param mixed $data
     *
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Schema instance missing.');
        }

        $field = $this->specializedFIelds[0];
        $returnData = [];

        foreach ($data->getTableDefinitions() as $definition) {
            $returnData[] = [
                $field => $definition->getName(),
                0 => $definition->getName(),
            ];
        }

        return $returnData;
    }
}
