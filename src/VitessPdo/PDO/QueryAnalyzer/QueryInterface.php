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

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;

/**
 * Description of class Query
 *
 * @author  mfris
 * @package VitessPdo\PDO\Analyzer
 */
interface QueryInterface
{

    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_CREATE = 'CREATE';
    const TYPE_ALTER = 'ALTER';
    const TYPE_DROP = 'DROP';
    const TYPE_USE    = 'USE';
    const TYPE_SHOW   = 'SHOW';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @return string
     */
    public function getSql();

    /**
     * @return array
     */
    public function getParsedSql();

    /**
     * @return bool
     */
    public function isInsert();

    /**
     * @return bool
     */
    public function isUpdate();

    /**
     * @return bool
     */
    public function isDelete();

    /**
     * @return bool
     */
    public function isWritable();

    /**
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    public function isType($type);
}
