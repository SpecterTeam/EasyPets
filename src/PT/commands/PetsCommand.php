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

namespace PT\commands;

use PT\Loader;
use PT\PetsManager\Pets;
use PT\PetsManager\PetsSession;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;


class PetsCommand extends Command implements PluginIdentifiableCommand
{

    public $plugin;

    const ALIASE_SUBCOMMAND_ADD = [
        "set",
        "add",
        "new"
    ];
    const ALIASE_SUBCOMMAND_REMOVE = [
        "remove",
        "del",
        "delete",
        "rm"
    ];

    /**
     * PetsCommand constructor.
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct('pets', 'Your pet follow you');
        $this->setUsage('/pets <add:remove:list>');
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        if ($this->plugin->isEnabled()) {
            if ($sender instanceof ConsoleCommandSender) {
                $sender->sendMessage('§cPlease run this command in game');
                return true;
            }

            if ($sender->hasPermission('pets.use.command')) {

                if (!isset($args[0])) {
                    $sender->sendMessage("§e--- §l§6Pets§r§e ---");
                    $sender->sendMessage("§a- §e/pets <list> §6List of Pet(s)");
                    $sender->sendMessage("§a- §e/pets <add> <name of pet> §6Set the pet.");
                    $sender->sendMessage("§a- §e/pets <remove> <name of pet> §6remove your current pet.");
                    return false;
                }

                if (isset($args[0])) {
                    if ($sender instanceof Player) {


                        if (in_array($args[0], self::ALIASE_SUBCOMMAND_ADD)) {
                            if (isset($args[1])) {

                                if (empty($args[1])) {
                                    $sender->sendMessage('§cplease enter the specific name of pet.');
                                    return true;
                                }


                                if (!is_numeric($args[1])) {

                                    if (!in_array($args[1], Pets::$petsAsStr)) {
                                        $sender->sendMessage("§cthis pet doesn't exist on our list, please use /pet list for see our list of pet(s)");
                                        return false;
                                    }


                                    $pets_name = Pets::$pets[$args[1]];


                                    if (isset($pets_name)) {
                                        PetsSession::setPets($sender, $args{1});
                                        $sender->sendMessage("§ayou have successfully added a pet " . $args[1]);

                                    } else {
                                        $sender->sendMessage("§cthis pets doesn't exist please use the command /pets list for see the list.");
                                    }
                                } else {

                                    $sender->sendMessage('§cplease enter the specific name of pet');
                                }
                            }
                        }

                        if ($args[0] == 'list') {

                            $i = 0;
                            $sender->sendMessage('§a>= List of Pets =<');
                            foreach (Pets::$petsAsStr as $name) {
                                $i++;
                                $sender->sendMessage('§c- ' . $i . '§a '.$name);
                            }
                        }

                        if (in_array($args[0], self::ALIASE_SUBCOMMAND_REMOVE)) {
                            if (isset($args[1])) {

                                if (empty($args[1])) {
                                    $sender->sendMessage('§cplease enter the specific name of pet.');
                                    return true;
                                }


                                if (!is_numeric($args[1])) {


                                    if (!in_array($args[1], Pets::$petsAsStr)) {
                                        $sender->sendMessage("§cthis pet doesn't exist on our list.");
                                        return false;
                                    }

                                    $name = Pets::$pets[$args[1]];

                                    if (PetsSession::getPetsPlayer($sender)) {

                                        if (PetsSession::getPetsPlayerName($sender) === $name) {
                                            PetsSession::removePets($sender);
                                            $sender->sendMessage("§ayou have removed the pet: " . $args[1]);

                                        } else {
                                            $sender->sendMessage("§cthe pet you put does not match the pet you have.");
                                        }

                                    } else {
                                        $sender->sendMessage("§cyou can't remove pets because you don't have any pet.");
                                    }
                                } else {
                                    $sender->sendMessage("§cplease enter the specific name of pet");
                                }

                            }
                        }
                    } else {
                        $sender->sendMessage("§cYou don't have the permission to use this command");
                    }
                }
            }
        }
        return true;
    }
}
