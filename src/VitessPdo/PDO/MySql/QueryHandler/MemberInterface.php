<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class MemberInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
interface MemberInterface
{

    /**
     * @param MemberInterface $successor
     *
     * @return void
     */
    public function setSuccessor(MemberInterface $successor);

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    public function handle(QueryInterface $query);
}
