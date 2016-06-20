<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Vitess;

use VitessPdo\PDO\QueryExecutor\CursorInterface;
use Vitess\Cursor as VitessCursor;

/**
 * Proxy for vitess cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
class Cursor implements CursorInterface
{

    /**
     * @var VitessCursor
     */
    private $cursor;

    /**
     * Cursor constructor.
     *
     * @param VitessCursor $cursor
     */
    public function __construct(VitessCursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @return int
     */
    public function getRowsAffected()
    {
        return $this->cursor->getRowsAffected();
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return $this->cursor->getInsertId();
    }

    /**
     * @return \Vitess\Proto\Query\Field[]
     */
    public function getFields()
    {
        return $this->cursor->getFields();
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->cursor->close();
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function next()
    {
        return $this->cursor->next();
    }
}
