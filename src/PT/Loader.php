<?php

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