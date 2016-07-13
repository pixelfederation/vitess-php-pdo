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

namespace VitessPdo\PDO\VtCtld\Result\GetSchema;

use VitessPdo\PDO\Exception;

/**
 * Description of class TableDefinition
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\GetSchema
 */
class TableDefinition
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const string
     */
    const KEY_NAME = 'name';

    /**
     * @const string
     */
    const KEY_SCHEMA = 'schema';

    /**
     * @const string
     */
    const KEY_COLUMNS = 'columns';

    /**
     * @const string
     */
    const KEY_PRIMARY_KEY_COLUMNS = 'primary_key_columns';

    /**
     * @const string
     */
    const KEY_TYPE = 'type';

    /**
     * @const string
     */
    const KEY_DATA_LENGTH = 'data_length';

    /**
     * TableDefinition constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getName()
    {
        if (!isset($this->data[self::KEY_NAME])) {
            throw new Exception('Name missing.');
        }

        return $this->data[self::KEY_NAME];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSchema()
    {
        if (!isset($this->data[self::KEY_SCHEMA])) {
            throw new Exception('Schema missing.');
        }

        return $this->data[self::KEY_SCHEMA];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getColumns()
    {
        if (!isset($this->data[self::KEY_COLUMNS])) {
            throw new Exception('Columns missing.');
        }

        return $this->data[self::KEY_COLUMNS];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPrimaryKeyColumns()
    {
        if (!isset($this->data[self::KEY_PRIMARY_KEY_COLUMNS])) {
            throw new Exception('Primary key columns missing.');
        }

        return $this->data[self::KEY_PRIMARY_KEY_COLUMNS];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getType()
    {
        if (!isset($this->data[self::KEY_TYPE])) {
            throw new Exception('Type missing.');
        }

        return $this->data[self::KEY_TYPE];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDataLength()
    {
        if (!isset($this->data[self::KEY_DATA_LENGTH])) {
            throw new Exception('Data length missing.');
        }

        return $this->data[self::KEY_DATA_LENGTH];
    }
}
