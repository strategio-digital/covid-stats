<?php

namespace App;

use App\Controller\Homepage;
use Symfony\Component\Dotenv\Dotenv;
use Tracy\Debugger;

class Bootstrap
{
    public function run() : void
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        
        Debugger::enable($_ENV['ENV_MODE'] === 'develop' ? Debugger::DEVELOPMENT : Debugger::PRODUCTION);
        
        $controller = new Homepage();
        $controller->index();
    }
}