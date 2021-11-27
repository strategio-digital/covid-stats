<?php

namespace App;

use App\Controller\Api;
use App\Controller\Homepage;
use Latte\Engine;
use Nette\Http\RequestFactory;
use Symfony\Component\Dotenv\Dotenv;
use Tracy\Debugger;

class Bootstrap
{
    public function run(): void
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        
        date_default_timezone_set($_ENV['TZ']);
        Debugger::enable($_ENV['ENV_MODE'] === 'develop' ? Debugger::DEVELOPMENT : Debugger::PRODUCTION);
    
        $latte = new Engine();
        $latte->setTempDirectory(__DIR__ . '/../temp/latte');
    
        $requestFactory = new RequestFactory();
        $request = $requestFactory->fromGlobals();
        
        if ($request->getUrl()->getPath() === '/api/death-detail') {
            $controller = new Api($request, $latte);
        } else {
            $controller = new Homepage($request, $latte);
        }
        
        $controller->index();
    }
}