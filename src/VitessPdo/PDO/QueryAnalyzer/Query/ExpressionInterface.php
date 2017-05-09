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
