<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\Vitess;

use Vitess\Cursor;
use Vitess\Exception as VitessException;

/**
 * Description of class Result
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
class Result
{

    /**
     * @var Cursor
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
     * Result constructor.
     *
     * @param Cursor               $cursor
     * @param VitessException|null $exception
     */
    public function __construct(Cursor $cursor = null, VitessException $exception = null)
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
     * @return Cursor
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
