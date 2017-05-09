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

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\VtCtld\Command\Parameter\Parameter;

/**
 * Description of class GetSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
class GetSchema extends Command
{

    /**
     * @const string
     */
    const PARAM_TABLET = 'tablet';

    /**
     * GetSchema constructor.
     *
     * @param string $tablet
     */
    public function __construct($tablet)
    {
        $this->set(self::PARAM_TABLET, new Parameter(self::PARAM_TABLET, $tablet));
    }
}
