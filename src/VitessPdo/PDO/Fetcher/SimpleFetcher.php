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

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class SimpleFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
abstract class SimpleFetcher implements FetcherInterface
{

    /**
     * @param $rows
     * @param $function
     *
     * @return array
     */
    protected function filterRowsKeysByFunction($rows, $function)
    {
        if (empty($rows)) {
            return [];
        }

        $strKeyEmptyArray = array_flip(array_filter(array_keys($rows[0]), $function));

        return array_map(function ($row) use ($strKeyEmptyArray) {
            return array_intersect_key($row, $strKeyEmptyArray);
        }, $rows);
    }
}
