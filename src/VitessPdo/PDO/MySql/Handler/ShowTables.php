<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class ShowTables
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowTables extends VtCtldBase
{

    /**
     * @param Query $query
     *
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(Query $query)
    {
        $vtCtldResult = $this->client->getVSchema();
        $cursor = new Cursor($vtCtldResult->getData(), $vtCtldResult->getFields());

        return new Result($cursor);
    }
}
