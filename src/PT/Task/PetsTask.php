<?php
/**
 *     EasyPets  Copyright (C) 2018  SpecterTeam
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
                $p->sendDataPacket($pk);
                Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);

            }
        }
    }
}
