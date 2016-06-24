<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
abstract class Chain
{

    /**
     * @var MemberInterface
     */
    protected $first;

    /**
     * Chain constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    public function getResult(QueryInterface $query)
    {
        return $this->first->handle($query);
    }

    /**
     * @return void
     */
    abstract protected function initialize();
}
