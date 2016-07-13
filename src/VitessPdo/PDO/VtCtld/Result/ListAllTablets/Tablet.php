<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
