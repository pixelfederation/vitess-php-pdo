<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
