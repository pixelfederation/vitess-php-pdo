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

namespace VitessPdo\PDO;

use PDO as CorePDO;

/**
 * Description of class ParamProcessor
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class ParamProcessor
{

    /**
     * @var array
     */
    private static $strReplaceFrom = ['\\', "\0", "\n", "\r", "'", "\x1a"];

    /**
     * @var array
     */
    private static $strReplaceTo = ['\\\\', '\\0', '\\n', '\\r', "''", '\\Z'];

    /**
     * @var array
     */
    private static $typeHandlers = [
        CorePDO::PARAM_BOOL => 'boolean',
        CorePDO::PARAM_INT => 'integer',
        CorePDO::PARAM_NULL => 'null',
        CorePDO::PARAM_STR => 'string',
        CorePDO::PARAM_LOB => 'string',
    ];

    /**
     * @param mixed $value
     * @param int   $type
     *
     * @return mixed
     * @throws Exception
     */
    public function process($value, $type = CorePDO::PARAM_STR)
    {
        if ($value === null) {
            return null;
        }

        $handler = $this->getHandler($type);

        return $this->{$handler}($value);
    }

    /**
     * @param mixed $value
     * @param int   $type
     *
     * @return mixed
     * @throws Exception
     */
    public function processEscaped($value, $type = CorePDO::PARAM_STR)
    {
        if (!in_array($type, [CorePDO::PARAM_STR, CorePDO::PARAM_LOB])) {
            return $this->process($value, $type);
        }

        return $this->stringEscaped($value);
    }

    /**
     * @param int $type
     *
     * @return null|string
     * @throws Exception
     */
    private function getHandler($type)
    {
        if (!isset(self::$typeHandlers[$type])) {
            throw new Exception("Unsupported PDO param type - " . $type);
        }

        return self::$typeHandlers[$type];
    }

    /**
     * @param mixed $value
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function boolean($value)
    {
        return (bool) $value;
    }

    /**
     * @param mixed $value
     *
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function integer($value)
    {
        return (int) $value;
    }

    /**
     * @param mixed $value
     *
     * @return null
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function null($value)
    {
        return null;
    }

    /**
     * @param mixed $value
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function string($value)
    {
        $value = (string) $value;

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function stringEscaped($value)
    {
        $value = $this->string($value);
        $value = str_replace(self::$strReplaceFrom, self::$strReplaceTo, $value);

        return $value;
    }
}
