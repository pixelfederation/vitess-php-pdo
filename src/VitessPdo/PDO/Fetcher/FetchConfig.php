<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

use PDO as CorePDO;

/**
 * Description of class FetchConfig
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class FetchConfig
{

    /**
     * @var int
     */
    private $fetchStyle = CorePDO::FETCH_BOTH;

    /**
     * @var mixed
     */
    private $fetchArgument = null;

    /**
     * @var array
     */
    private $ctorArgs = [];

    /**
     * FetchConfig constructor.
     *
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     */
    public function __construct($fetchStyle, $fetchArgument = null, array $ctorArgs = [])
    {
        $this->fetchStyle    = $fetchStyle;
        $this->fetchArgument = $fetchArgument;
        $this->ctorArgs      = $ctorArgs;
    }

    /**
     * @return int
     */
    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * @return mixed
     */
    public function getFetchArgument()
    {
        return $this->fetchArgument;
    }

    /**
     * @return array
     */
    public function getCtorArgs()
    {
        return $this->ctorArgs;
    }
}
