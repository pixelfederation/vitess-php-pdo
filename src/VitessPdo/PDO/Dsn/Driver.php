<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\Dsn;

use VitessPdo\PDO\Exception;

/**
 * Description of class Driver
 *
 * @author  mfris
 * @package VitessPdo\PDO\Dsn
 */
class Driver
{

    /**
     * @var string
     */
    private $protocol;

    /**
     * @const string
     */
    const PROTOCOLL_VITESS = 'vitess';

    /**
     * Driver constructor.
     *
     * @param string $protocol
     * @throws Exception
     */
    public function __construct($protocol)
    {
        $this->protocol = $protocol;

        if ($protocol !== self::PROTOCOLL_VITESS) {
            throw new Exception("Invalid protocoll name - only 'vitess' is allowed.");
        }
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}
