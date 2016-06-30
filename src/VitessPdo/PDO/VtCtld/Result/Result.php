<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Dsn\Dsn;

/**
 * Description of class Result
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
abstract class Result
{

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var Dsn
     */
    protected $dsn;

    /**
     * @var string
     */
    protected $responseString;

    /**
     * Result constructor.
     *
     * @param Dsn    $dsn
     * @param string $responseString
     */
    public function __construct(Dsn $dsn, $responseString)
    {
        $this->dsn            = $dsn;
        $this->responseString = $responseString;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->parse();
        }

        return $this->data;
    }

    /**
     */
    abstract protected function parse();
}
