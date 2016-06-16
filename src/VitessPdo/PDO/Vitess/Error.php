<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo\PDO\Vitess;

use Vitess\Exception as VitessException;

/**
 * Description of class Error
 *
 * @author  mfris
 * @package ${NAMESPACE}
 */
final class Error
{

    /**
     * @var VitessException
     */
    private $exception;

    /**
     * Error constructor.
     *
     * @param VitessException $exception
     */
    public function __construct(VitessException $exception)
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
