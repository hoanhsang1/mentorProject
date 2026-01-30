 <?php

require_once __DIR__ . '/Router.php';

class App
{
    public function run()
    {
        $router = new Router();

        require_once __DIR__ . '/../routes.php';

        $router->dispatch();
    }
}
