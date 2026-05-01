<?php

namespace Tasks\Runners;

use Database\Connection;
use Database\ViewManager;
use Tasks\AbstractRunner;

class Views extends AbstractRunner
{
    public function etirun($args): void
    {
        ViewManager::syncAll();
    }
}