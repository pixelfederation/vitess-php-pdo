<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Vitess;

use Vitess\Proto\Topodata\TabletType;

/**
 * Description of class ClusterConfig
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
class ClusterConfig
{

    /**
     * @var int
     */
    private $readFrom = TabletType::REPLICA;

    /**
     * @var string
     */
    private $readFromReadable = self::READ_FROM_REPLICA;

    /**
     * @const string
     */
    const READ_FROM_REPLICA = 'replica';

    /**
     * @const string
     */
    const READ_FROM_MASTER = 'master';

    /**
     * @return ClusterConfig
     */
    public function readFromMaster()
    {
        $this->readFrom = TabletType::MASTER;
        $this->readFromReadable = self::READ_FROM_MASTER;

        return $this;
    }

    /**
     * @return ClusterConfig
     */
    public function readFromReplica()
    {
        $this->readFrom = TabletType::REPLICA;
        $this->readFromReadable = self::READ_FROM_REPLICA;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadFrom()
    {
        return $this->readFrom;
    }

    /**
     * @return string
     */
    public function getReadFromHumanReadable()
    {
        return $this->readFromReadable;
    }
}
