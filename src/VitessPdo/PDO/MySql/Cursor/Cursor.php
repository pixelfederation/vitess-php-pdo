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
