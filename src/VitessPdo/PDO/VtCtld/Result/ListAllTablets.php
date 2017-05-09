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

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets\Tablet;

/**
 * Description of class ListAllTablets
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class ListAllTablets extends Result
{

    /**
     * @return Tablet[]
     */
    public function getDataForCurrentKeyspace()
    {
        $keyspace = $this->dsn->getConfig()->getKeyspace();

        return array_values(
            array_filter($this->getData(), function (Tablet $tablet) use ($keyspace) {
                return $tablet->getKeyspace() === $keyspace;
            })
        );
    }

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $tabletRows = explode("\n", trim($this->responseString));

        $this->data = array_map(function ($tabletRow) {
            return new Tablet(explode(' ', $tabletRow));
        }, $tabletRows);
    }
}
