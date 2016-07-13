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

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper\Tablet;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\Result\Show\IndexFrom;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Result\GetSchema as GetSchemaResult;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class IndexFromMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain
 */
class IndexFromMember extends VtCtldMember
{

    /**
     * @var Tablet
     */
    private $tablet;

    /**
     * TablesMember constructor.
     *
     * @param ClientInterface $client
     * @param Tablet $tablet
     */
    public function __construct(ClientInterface $client, Tablet $tablet)
    {
        $this->tablet = $tablet;
        parent::__construct($client);
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_INDEX) {
            return null;
        }

        $fromExpr = $query->getFromExpression();

        if (!$fromExpr) {
            throw new Exception('From expression missing.');
        }

        $tablet = $this->tablet->getTablet();
        $schemaCmd = new GetSchema($tablet->getAlias());
        /* @var $result GetSchemaResult */
        $result = $this->client->executeCommand($schemaCmd);

        return new IndexFrom($result, $fromExpr);
    }
}
