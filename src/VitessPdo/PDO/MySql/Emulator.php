<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\MySql;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\Handler\HandlerInterface;
use VitessPdo\PDO\MySql\Handler\QueryUse;
use VitessPdo\PDO\MySql\Handler\ShowTables;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\VtCtld\Client;
use ArrayObject;

/**
 * Description of class QueryChain
 *
 * @author  mfris
 * @package Adminer\Vitess
 */
class Emulator
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var ArrayObject
     */
    private $handlers;

    /**
     * @var array
     */
    private static $handlerTypes = [
        Query::TYPE_SHOW => 'getShowExpression',
    ];

    /**
     * QueryChain constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
        $this->handlers = $this->initializeHandlers();
    }

    /**
     * @param Query $query
     *
     * @return Result|null
     */
    public function getResult(Query $query)
    {
        /* @var $handler HandlerInterface */
        $handler = null;

        $type = $query->getType();
        if (!$this->handlers->offsetExists($type)) {
            return null;
        }

        $handler = $this->handlers->offsetGet($type);

        if ($handler && !$handler instanceof HandlerInterface) {
            $handler = $this->getHandlerByType($query, $type);
        }

        if ($handler) {
            return $handler->getResult($query);
        }

        return null;
    }

    /**
     * @param Query $query
     * @param       $type
     *
     * @return HandlerInterface|null
     */
    private function getHandlerByType(Query $query, $type)
    {
        if (!isset(self::$handlerTypes[$type])) {
            return null;
        }

        /* @var $handlers ArrayObject */
        $handlers = $this->handlers->offsetGet($type);
        $handlerFn = self::$handlerTypes[$type];
        $handlerKey = $query->{$handlerFn}();

        if (!$handlers->offsetExists($handlerKey)) {
            return null;
        }

        return $handlers->offsetGet($handlerKey);
    }

    /**
     * @return ArrayObject
     */
    private function initializeHandlers()
    {
        $vtCtldClient = new Client($this->dsn);

        $members = new ArrayObject();
        $members->offsetSet(Query::TYPE_USE, new QueryUse($this->dsn));
        $membersShow = new ArrayObject();
        $members->offsetSet(Query::TYPE_SHOW, $membersShow);
        $membersShow->offsetSet(Query::SHOW_EXPRESSION_TABLES, new ShowTables($vtCtldClient));

        return $members;
    }
}
