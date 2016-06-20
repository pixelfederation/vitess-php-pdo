<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryExecutor;

use Exception;

/**
 * Description of class Query
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
abstract class Result implements ResultInterface
{

    /**
     * @var CursorInterface
     */
    private $cursor;

    /**
     * @var Error
     */
    private $error;

    /**
     * @const string
     */
    const DEFAULT_LAST_INSERT_ID = '0';

    /**
     * Query constructor.
     *
     * @param CursorInterface      $cursor
     * @param Exception|null $exception
     */
    public function __construct(CursorInterface $cursor = null, Exception $exception = null)
    {
        $this->cursor = $cursor;

        if ($exception) {
            $this->error = new Error($exception);
        }
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->error === null;
    }

    /**
     * @return CursorInterface
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int|string
     */
    public function getLastInsertId()
    {
        if (!$this->cursor) {
            return self::DEFAULT_LAST_INSERT_ID;
        }

        return $this->cursor->getInsertId();
    }
}
