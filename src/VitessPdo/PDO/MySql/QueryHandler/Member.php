<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class Member
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class Member implements MemberInterface
{

    /**
     * @var MemberInterface
     */
    private $successor;

    /**
     * @param MemberInterface $successor
     *
     * @return MemberInterface
     */
    public function setSuccessor(MemberInterface $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    final public function handle(QueryInterface $query)
    {
        $result = $this->process($query);

        if ($result === null && $this->successor !== null) {
            $result = $this->successor->handle($query);
        }

        return $result;
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    abstract protected function process(QueryInterface $query);

    /**
     * @param array $data
     * @param array $fields
     *
     * @return Result
     */
    protected function getResultFromData(array $data, array $fields = [])
    {
        $cursor = new Cursor($data, $fields);

        return new Result($cursor);
    }
}
