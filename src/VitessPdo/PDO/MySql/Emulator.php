<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\MySql;

use VitessPdo\PDO\MySql\Handler\HandlerInterface;
use VitessPdo\PDO\MySql\Handler\QueryUse;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer\Query as Query;
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
     * @var ArrayObject
     */
    private $handlers;

    /**
     * @var string[]
     */
    private static $handlersSteps = [
        'getType',
    ];

    /**
     * QueryChain constructor.
     */
    public function __construct()
    {
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
        $handlers = $this->handlers;

        foreach (self::$handlersSteps as $step) {
            $stepValue = $query->{$step}();
            if (!$handlers->offsetExists($stepValue)) {
                break;
            }

            $handlers = $handlers->offsetGet($stepValue);

            if ($handlers instanceof HandlerInterface) {
                $handler = $handlers;
                break;
            } elseif (!$handlers) {
                break;
            }
        }

        if ($handler) {
            return $handler->getResult($query);
        }

        return null;
    }

    /**
     * @return ArrayObject
     */
    private function initializeHandlers()
    {
        $members = new ArrayObject();
        $members->offsetSet(Query::TYPE_USE, new QueryUse());

        return $members;
    }
}
