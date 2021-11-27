<?php

namespace App;

use App\Controller\Homepage;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Tracy\Debugger;

class Bootstrap
{
    public function run() : void
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        
        date_default_timezone_set($_ENV['TZ']);
        Debugger::enable($_ENV['ENV_MODE'] === 'develop' ? Debugger::DEVELOPMENT : Debugger::PRODUCTION);
    
        $request = Request::createFromGlobals();
        $controller = new Homepage($request);
        $controller->index();
    }
}