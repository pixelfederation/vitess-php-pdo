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
