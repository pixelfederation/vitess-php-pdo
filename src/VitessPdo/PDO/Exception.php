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

use Exception as CoreException;

/**
 * Description of class Exception
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Exception extends CoreException
{

    /**
     * @throws Exception
     */
    public static function newStatementClassException()
    {
        throw new Exception(
            "General error: PDO::ATTR_STATEMENT_CLASS requires format array(classname, array(ctor_args)); "
            . "the classname must be a string specifying an existing class"
        );
    }
}
