<?php

require 'AppServer/autoload.php';
use MFS\AppServer\Apps\FileServe\FileServe as file_server;
use MFS\AppServer\Middleware\PHP_Compat\PHP_Compat as aip_php_compat;

require __DIR__.'/appserv_app.php';

try {
    $app = new aip_php_compat(new midgardmvc_appserv_app());

    $_midcom_root = realpath(__DIR__.'/../..').'/';

    $map = new \MFS\AppServer\Middleware\URLMap\URLMap(array(
        '/' => $app,
        '/midcom-static/midgardmvc_core'                => new file_server($_midcom_root.'midgardmvc_core/static', 4000000),
        '/midcom-static/midgardmvc_helper_datamanager'  => new file_server($_midcom_root.'midgardmvc_helper_datamanager/static'),
        '/midcom-static/net_nemein_dasboard'            => new file_server($_midcom_root.'net_nemein_dasboard/static'),
    ));

    $handler = new \MFS\AppServer\DaemonicHandler('tcp://127.0.0.1:8080', 'HTTP');
    $handler->serve($map);
} catch (Exception $e) {
    echo $e;
}