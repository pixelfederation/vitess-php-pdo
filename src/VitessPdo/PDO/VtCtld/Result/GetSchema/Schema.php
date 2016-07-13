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
 * Description of class Schema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\GetSchema
 */
final class Schema
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var TableDefinition[]
     */
    private $definitions;

    /**
     * @const string
     */
    const KEY_DATABASE_SCHEMA = 'database_schema';

    /**
     * @const string
     */
    const KEY_TABLE_DEFINITIONS = 'table_definitions';

    /**
     * Schema constructor.
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
    public function getDatabaseSchema()
    {
        if (!isset($this->data[self::KEY_DATABASE_SCHEMA])) {
            throw new Exception('Database schema missing.');
        }

        return $this->data[self::KEY_DATABASE_SCHEMA];
    }

    /**
     * @return array|TableDefinition[]
     * @throws Exception
     */
    public function getTableDefinitions()
    {
        if (!isset($this->data[self::KEY_TABLE_DEFINITIONS])) {
            throw new Exception('Table definitions schema missing.');
        }

        if ($this->definitions === null) {
            $this->definitions = array_map(function (array $definitionData) {
                return new TableDefinition($definitionData);
            }, $this->data[self::KEY_TABLE_DEFINITIONS]);
        }

        return $this->definitions;
    }

    /**
     * @param string $table
     *
     * @return null|TableDefinition
     * @throws Exception
     */
    public function getTableDefinition($table)
    {
        foreach ($this->getTableDefinitions() as $definition) {
            if ($definition->getName() === $table) {
                return $definition;
            }
        }

        return null;
    }
}
