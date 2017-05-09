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

namespace VitessPdo\PDO\MySql\Cursor;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryExecutor\CursorInterface;

/**
 * Description of class Cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Cursor
 */
class Cursor implements CursorInterface
{

    /**
     * @var array
     */
    private $rows;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var int
     */
    private $rowsAffected;

    /**
     * @var int
     */
    private $insertId;

    /**
     * @var int
     */
    private $currentIndex = 0;

    /**
     * Cursor constructor.
     *
     * @param array $rows
     * @param array $fields
     * @param int $rowsAffected
     * @param int $insertId
     */
    public function __construct(array $rows, array $fields = [], $rowsAffected = 0, $insertId = 0)
    {
        $this->rows = $rows;
        $this->fields = $fields;
        $this->rowsAffected = (int) $rowsAffected;
        $this->insertId = (int) $insertId;
    }

    /**
     * @return int
     */
    public function getRowsAffected()
    {
        return $this->rowsAffected;
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return void
     */
    public function close()
    {
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function next()
    {
        if (!isset($this->rows[$this->currentIndex])) {
            return false;
        }

        return $this->rows[$this->currentIndex++];
    }
}
