<?php
/**
 * Created by PhpStorm.
 * User: Walid
 * Date: 21/05/2018
 * Time: 16:08
 */
namespace PT\Task;


use pocketmine\network\mcpe\protocol\MoveEntityAbsolutePacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use PT\Loader;
use PT\PetsManager\PetsSession;

class PetsTask extends Task
{

    public $plugin;

    /**
     * PetsTask constructor.
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }


    /**
     * @param int $refreshTick
     */
    public function onRun(int $refreshTick)
    {

        foreach (Server::getInstance()->getOnlinePlayers() as $p) {

            if (PetsSession::getPetsPlayer($p)) {

                $pk = new MoveEntityAbsolutePacket();
                $pk->entityRuntimeId = PetsSession::$players[$p->getName()]["entity"];
                $pk->position = $p->asVector3()->add(0, 3, 0);
                $pk->xRot = $p->pitch;
                $pk->yRot = $p->yaw;
                $pk->zRot = $p->yaw;
                $pk->flags = MoveEntityAbsolutePacket::FLAG_TELEPORT;
                $p->dataPacket($pk);
                Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);

            }
        }
    }
}