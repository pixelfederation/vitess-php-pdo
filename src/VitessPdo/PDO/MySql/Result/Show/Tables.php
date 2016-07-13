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

namespace VitessPdo\PDO\MySql\Result\Show;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class Tables
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class Tables extends VtCtldResult
{

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     */
    public function __construct(GetSchema $result)
    {
        $this->initFields($result->getKeyspace());
        parent::__construct($result);
    }

    /**
     * @param string $keyspace
     * @return void
     */
    private function initFields($keyspace)
    {
        $this->specializedFIelds = [
            "Tables_in_{$keyspace}", 0,
        ];
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Schema instance missing.');
        }

        $field = $this->specializedFIelds[0];
        $returnData = [];

        foreach ($data->getTableDefinitions() as $definition) {
            $returnData[] = [
                $field => $definition->getName(),
                0 => $definition->getName(),
            ];
        }

        return $returnData;
    }
}
