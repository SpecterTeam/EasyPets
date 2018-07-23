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

namespace PT;


use pocketmine\plugin\PluginBase as PL;

use pocketmine\event\Listener;
use PT\commands\PetsCommand;
use PT\Task\PetsTask;

class Loader extends PL implements Listener
{

    public function onEnable()
    {
        $this->registerTask();
        $this->registerCommands();
    }


    public function registerCommands()
    {
        $PetsCmd = new PetsCommand($this);
        $this->getServer()->getCommandMap()->register("pets", $PetsCmd);
    }

    public function registerTask()
    {
        $this->getScheduler()->scheduleRepeatingTask(new PetsTask($this), 15);
    }
}
