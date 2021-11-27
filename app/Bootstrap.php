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
        
        $request = Request::createFromGlobals();
        
        Debugger::enable($_ENV['ENV_MODE'] === 'develop' ? Debugger::DEVELOPMENT : Debugger::PRODUCTION);
        
        $controller = new Homepage($request);
        $controller->index();
    }
}