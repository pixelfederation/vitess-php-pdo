<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryExecutor;

use Exception;

/**
 * Description of class Error
 *
 * @author  mfris
 * @package ${NAMESPACE}
 */
final class Error
{

    /**
     * @var Exception
     */
    private $exception;

    /**
     * Error constructor.
     *
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return array
     */
    public function getInfoAsArray()
    {
        return [
            $this->exception->getCode(),
            $this->exception->getCode(),
            $this->exception->getMessage(),
        ];
    }
}
