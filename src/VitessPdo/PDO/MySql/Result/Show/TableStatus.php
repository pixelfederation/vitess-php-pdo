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
 * Description of class TableStatus
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class TableStatus extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        'Name',
        'Engine',
        'Version',
        'Row_format',
        'Rows',
        'Avg_row_length',
        'Data_length',
        'Max_data_length',
        'Index_length',
        'Data_free',
        'Auto_increment',
        'Create_time',
        'Update_time',
        'Check_time',
        'Collation',
        'Checksum',
        'Create_options',
        'Comment',
    ];

    /**
     * @var array
     */
    private static $tplData = [
        'Name' => 'dummy',
        0 => 'dummy',
        'Engine' => 'InnoDB',
        1 => 'InnoDB',
        'Version' => '10',
        2 => '10',
        'Row_format' => 'Compact',
        3 => 'Compact',
        'Rows' => '0',
        4 => '0',
        'Avg_row_length' => '0',
        5 => '0',
        'Data_length' => '0',
        6 => '0',
        'Max_data_length' => '0',
        7 => '0',
        'Index_length' => '0',
        8 => '0',
        'Data_free' => '0',
        9 => '0',
        'Auto_increment' => null,
        10 => null,
        'Create_time' => '2016-06-15 13:12:59',
        11 => '2016-06-15 13:12:59',
        'Update_time' => null,
        12 => null,
        'Check_time' => null,
        13 => null,
        'Collation' => 'utf8_bin',
        14 => 'utf8_bin',
        'Checksum' => null,
        15 => null,
        'Create_options' => '',
        16 => '',
        'Comment' => '',
        17 => '',
    ];

    /**
     * @var string
     */
    private $likeExpr;

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string     $fromExpr
     */
    public function __construct(GetSchema $result, $fromExpr = '')
    {
        $this->likeExpr = trim($fromExpr);
        parent::__construct($result);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Schema instance missing.');
        }

        $returnData = [];

        foreach ($data->getTableDefinitions() as $definition) {
            $tmp = self::$tplData;
            $tmp['Name'] = $tmp[0] = $definition->getName();
            $returnData[] = $tmp;
        }

        if ($this->likeExpr !== '') {
            $returnData = $this->filterData($returnData);
        }

        return array_values($returnData);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function filterData(array $data)
    {
        $pattern = '/^' . str_replace('%', '.*', trim($this->likeExpr, "'")) . '$/';
        $newData = [];

        foreach ($data as $row) {
            if (preg_match($pattern, $row['Name'])) {
                $newData[] = $row;
            }
        }

        return $newData;
    }
}
