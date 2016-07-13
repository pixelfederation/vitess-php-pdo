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

namespace VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class Field
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
interface ExpressionInterface
{

    /**
     * @const string
     */
    const KEY_EXPR_TYPE = 'expr_type';

    /**
     * @const string
     */
    const KEY_ALIAS = 'alias';

    /**
     * @const string
     */
    const KEY_BASE_EXPR = 'base_expr';

    /**
     * @const string
     */
    const KEY_SUB_TREE = 'sub_tree';

    /**
     * @const string
     */
    const KEY_CREATE_DEF = 'create-def';

    /**
     * @const string
     */
    const KEY_DELIM = 'delim';

    /**
     * @const string
     */
    const KEY_UNIQUE = 'unique';

    /**
     * @const string
     */
    const KEY_NULLABLE = 'nullable';

    /**
     * @const string
     */
    const KEY_AUTO_INC = 'auto_inc';

    /**
     * @const string
     */
    const KEY_PRIMARY = 'primary';

    /**
     * @const string
     */
    const KEY_DEFAULT = 'default';

    /**
     * @const string
     */
    const KEY_NO_QUOTES = 'no_quotes';

    /**
     * @const string
     */
    const TYPE_FUNCTION = 'function';

    /**
     * @const string
     */
    const TYPE_COLUMN_DEF = 'column-def';

    /**
     * @const string
     */
    const TYPE_COLUMN_LIST = 'column-list';

    /**
     * @const string
     */
    const TYPE_COLUMN_TYPE = 'column-type';

    /**
     * @const string
     */
    const TYPE_COLUMN_REF = 'colref';

    /**
     * @const string
     */
    const TYPE_PRIMARY_KEY = 'primary-key';

    /**
     * @const string
     */
    const TYPE_BRACKET_EXPRESSION = 'bracket_expression';

    /**
     * @const string
     */
    const TYPE_INDEX = 'index';

    /**
     * @const string
     */
    const TYPE_CONST = 'const';

    /**
     * @const string
     */
    const TYPE_RESERVED = 'reserved';

    /**
     * @const string
     */
    const TYPE_DATA_TYPE = 'data-type';

    /**
     * @const string
     */
    const TYPE_DEFAULT_VALUE = 'default-value';

    /**
     * @const string
     */
    const EXPR_USER = 'USER';

    /**
     * @const string
     */
    const EXPR_CONNECTION_ID = 'CONNECTION_ID';

    /**
     * @const string
     */
    const EXPR_LIKE = 'LIKE';

    /**
     * @const string
     */
    const EXPR_FROM = 'FROM';

    /**
     * @const string
     */
    const EXPR_NOT = 'NOT';

    /**
     * @const string
     */
    const EXPR_NULL = 'NULL';

    /**
     * @const string
     */
    const EXPR_DEFAULT = 'DEFAULT';

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string|false
     */
    public function getAlias();

    /**
     * @return string
     */
    public function getExpression();

    /**
     * @return Expression[]|false
     */
    public function getSubTree();

    /**
     * @return CreateExpression|false
     */
    public function getCreateDef();

    /**
     * @return string|false
     */
    public function getDelim();

    /**
     * @return NoQuotes|false
     */
    public function getNoQuotes();

    /**
     * @param string $type
     *
     * @return null|ExpressionInterface
     */
    public function findFirstInSubTree($type);

    /**
     * @param string $type
     *
     * @return ExpressionInterface[]
     */
    public function findAllInSubTreeAfterInclusive($type);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getData($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key);
}
