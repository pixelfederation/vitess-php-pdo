<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class ShowIndexFrom
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowIndexFrom extends VtCtldBase
{

    /**
     * @param QueryInterface $query
     *
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(QueryInterface $query)
    {
        $vtCtldResult = $this->client->getVSchema();
        $cursor = new Cursor($vtCtldResult->getData(), $vtCtldResult->getFields());

        return new Result($cursor);
    }
}
