<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\MySql;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\Handler\HandlerInterface;
use VitessPdo\PDO\MySql\Handler\QueryUse;
use VitessPdo\PDO\MySql\Handler\ShowCollation;
use VitessPdo\PDO\MySql\Handler\ShowCreateDatabase;
use VitessPdo\PDO\MySql\Handler\ShowTables;
use VitessPdo\PDO\MySql\Handler\ShowTableStatus;
use VitessPdo\PDO\MySql\Handler\ShowTableStatusLike;
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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        Query::TYPE_SHOW => [
            ['getExpressionForType', [Query::TYPE_SHOW, 0]],
            ['getExpressionForType', [Query::TYPE_SHOW, 1]],
            ['getExpressionForType', [Query::TYPE_SHOW, 2]],
        ],
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
        $index = 0;

        do {
            $handlerFn = isset(self::$handlerTypes[$type][$index]) ? self::$handlerTypes[$type][$index] : null;
            $index++;

            if (!$handlerFn) {
                return null;
            }

            $handlerKey = call_user_func_array([$query, $handlerFn[0]], $handlerFn[1]);

            if (!$handlers->offsetExists($handlerKey)) {
                return null;
            }

            $handlers = $handlers->offsetGet($handlerKey);
        } while ($handlers instanceof ArrayObject);

        return $handlers;
    }

    /**
     * @return ArrayObject
     */
    private function initializeHandlers()
    {
        $vtCtldClient = new Client($this->dsn);

        $members = new ArrayObject();
        $members->offsetSet(Query::TYPE_USE, new QueryUse($this->dsn));
        $show = new ArrayObject();
        $members->offsetSet(Query::TYPE_SHOW, $show);
        $show->offsetSet(Query::EXPRESSION_TABLES, new ShowTables($vtCtldClient));
        $show->offsetSet(Query::EXPRESSION_COLLATION, new ShowCollation());
        $showCreate = new ArrayObject();
        $show->offsetSet(Query::EXPRESSION_CREATE, $showCreate);
        $showCreate->offsetSet(Query::EXPRESSION_DATABASE, new ShowCreateDatabase($this->dsn));
        $showTable = new ArrayObject();
        $show->offsetSet(Query::EXPRESSION_TABLE, $showTable);
        $showTableStatus = new ArrayObject();
        $showTable->offsetSet(Query::EXPRESSION_STATUS, $showTableStatus);
        $showTSHandler = new ShowTableStatus($vtCtldClient);
        $showTableStatus->offsetSet(0, $showTSHandler);
        $showTableStatus->offsetSet(Query::EXPRESSION_LIKE, new ShowTableStatusLike($vtCtldClient));

        return $members;
    }
}
