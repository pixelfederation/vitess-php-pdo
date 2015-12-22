<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo;

use VitessPdo\PDO\Dsn;
use PDOException;

/**
 * Description of class PDO
 *
 * @author  mfris
 * @package VitessPdo
 */
class PDO
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * PDO constructor.
     *
     * @param       $dsn
     * @param null  $username
     * @param null  $password
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $this->dsn = new Dsn($dsn);
    }
}
