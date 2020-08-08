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

namespace PT\PetsManager;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Server;
use pocketmine\Player;

class PetsSession
{
    public static $players = [];

    const BIGGEST_PETS_BY_ID = [
        "53",
        "41"
    ];


    /**
     * @param Player $player
     * @return bool|mixed
     */
    public static function getPetsPlayerName(Player $player)
    {
        if (isset(PetsSession::$players[$player->getName()])) {
            return PetsSession::$players[$player->getName()]["id"];
        }
        return false;
    }

    /**
     * @param Player $player
     * @return bool|mixed
     */
    public static function getPetsPlayer(Player $player)
    {
        return PetsSession::$players[$player->getName()] ?? false;
    }


    /**
     * @param Player $player
     * @param string $name
     */
    public static function setPets(Player $player, string $name)
    {
        if (isset(Pets::$pets[$name])) {
            self::removePets($player);
            PetsSession::$players[$player->getName()]["entity"] = Entity::$entityCount++;
            PetsSession::$players[$player->getName()]["id"] = Pets::$pets[$name];

            $pk = new AddActorPacket();
            $pk->entityRuntimeId = PetsSession::$players[$player->getName()]["entity"];
            $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[PetsSession::$players[$player->getName()]["id"]];
            $pk->position = $player->asVector3()->add(0, 3, 0);
            $pk->motion = $this->getMotion();
            $pk->yaw = $player->getYaw();
            $pk->pitch = $player->getPitch();
            if (in_array(PetsSession::$players[$player->getName()]["id"], self::BIGGEST_PETS_BY_ID)) {
                $pk->metadata = [Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0.5]];
            }

            Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function removePets(Player $player)
    {

        if (isset(PetsSession::$players[$player->getName()])) {

            $pk = new RemoveActorPacket();
            $pk->entityUniqueId = PetsSession::$players[$player->getName()]["entity"];
            $player->sendDataPacket($pk);

            Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk);
            unset(PetsSession::$players[$player->getName()]);

            return true;
        }
        return false;
    }

}
