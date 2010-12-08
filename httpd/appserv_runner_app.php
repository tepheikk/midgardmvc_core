<?php

use MFS\AppServer\Apps\FileServe\FileServe as file_server;
use MFS\AppServer\Middleware\PHP_Compat\PHP_Compat as aip_php_compat;
use MFS\AppServer\Middleware\Logger\Logger as aip_logger;
use MFS\AppServer\Middleware\Session\Session as aip_session;

require __DIR__.'/appserv_app.php';

class midgardmvc_appserv_runner_app
{
    private $app;

    public function __construct()
    {
        $urlmap = array(
            '/' => new aip_php_compat(new aip_session(new midgardmvc_appserv_app())),
            '/favicon.ico' => function($ctx) { return array(404, array(), ''); },
        );

        // add paths of components
        $_midgardmvc_root = realpath(__DIR__ . '/../..').'/';
        foreach (midgardmvc_core::get_instance()->component->get_components() as $component) {
            if (!file_exists("{$_midgardmvc_root}{$component->name}/static")) {
                continue;
            }

            $urlmap["/midgardmvc-static/{$component->name}"] = new file_server("{$_midgardmvc_root}{$component->name}/static", 4000000);
        }

        $map = new \MFS\AppServer\Middleware\URLMap\URLMap($urlmap);

        // we also want logging
        $this->app = new aip_logger($map, STDOUT);
    }

    public function __invoke($context)
    {
        // just run the application
        $app = $this->app;

        return $app($context);
    }
}
