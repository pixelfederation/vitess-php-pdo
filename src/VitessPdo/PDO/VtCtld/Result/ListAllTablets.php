<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets\Tablet;

/**
 * Description of class ListAllTablets
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class ListAllTablets extends Result
{

    /**
     * @return Tablet[]
     */
    public function getDataForCurrentKeyspace()
    {
        $keyspace = $this->dsn->getConfig()->getKeyspace();

        return array_values(
            array_filter($this->getData(), function (Tablet $tablet) use ($keyspace) {
                return $tablet->getKeyspace() === $keyspace;
            })
        );
    }

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $tabletRows = explode("\n", trim($this->responseString));

        $this->data = array_map(function ($tabletRow) {
            return new Tablet(explode(' ', $tabletRow));
        }, $tabletRows);
    }
}
