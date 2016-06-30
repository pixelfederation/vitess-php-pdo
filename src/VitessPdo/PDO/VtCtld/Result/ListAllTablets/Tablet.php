<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result\ListAllTablets;

/**
 * Description of class Tablet
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\ListAllTablets
 */
final class Tablet
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const int
     */
    const KEY_ALIAS = 0;

    /**
     * @const int
     */
    const KEY_KEYSPACE = 1;

    /**
     * @const int
     */
    const KEY_SHARD = 2;

    /**
     * @const int
     */
    const KEY_TYPE = 3;

    /**
     * @const int
     */
    const KEY_CONNECTION_GRPC = 4;

    /**
     * @const int
     */
    const KEY_CONNECTION_HTTP = 5;

    /**
     * Tablet constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->data[self::KEY_ALIAS];
    }

    /**
     * @return string
     */
    public function getKeyspace()
    {
        return $this->data[self::KEY_KEYSPACE];
    }

    /**
     * @return string
     */
    public function getShard()
    {
        return $this->data[self::KEY_SHARD];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->data[self::KEY_TYPE];
    }

    /**
     * @return string
     */
    public function getConnectionGrpc()
    {
        return $this->data[self::KEY_CONNECTION_GRPC];
    }

    /**
     * @return string
     */
    public function getConnectionHttp()
    {
        return $this->data[self::KEY_CONNECTION_HTTP];
    }
}
