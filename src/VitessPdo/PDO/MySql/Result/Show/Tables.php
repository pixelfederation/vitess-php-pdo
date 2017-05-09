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
