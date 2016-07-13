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
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class CreateTable
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class CreateTable extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        self::KEY_TABLE,
        self::KEY_CREATE_TABLE,
    ];

    /**
     * @var string
     */
    private $table;

    const KEY_TABLE = 'Table';
    const KEY_CREATE_TABLE = 'Create Table';

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string    $table
     */
    public function __construct(GetSchema $result, $table)
    {
        $this->table = trim($table);
        parent::__construct($result);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Data is not a Schema.');
        }

        $definition = $data->getTableDefinition($this->table);
        $defSchema = $definition->getSchema();

        $returnData = [];
        $row = [
            self::KEY_TABLE => $definition->getName(),
            0 => $definition->getName(),
            self::KEY_CREATE_TABLE => $defSchema,
            1 => $defSchema,
        ];
        $returnData[] = $row;

        return $returnData;
    }
}
