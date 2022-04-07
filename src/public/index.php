<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
require 'vendor/autoload.php';
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Http\Response\Cookies;
use Phalcon\Config\ConfigFactory;
use Phalcon\Logger\AdapterFactory;
use Phalcon\Logger\LoggerFactory;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Cache;
use Phalcon\Storage\SerializerFactory;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

// ------------------------------------------------Registering directories-----------------------------------

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
// ------------------------------------------------Registering complete-----------------------------------


// ------------------------------------------------Registering namespaces-----------------------------------

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/components',
        'App\Listeners' => APP_PATH . '/listeners',
    ]
);
// ------------------------------------------------Registering complete-----------------------------------


$loader->register();


$container = new FactoryDefault();
$application = new Application($container);

// ------------------------------------------------view container-----------------------------------


$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

// ------------------------------------------------view end-----------------------------------

$eventsManager = new eventsManager ;

// ------------------------------------------------Beforehandlerequest-----------------------------------


// $eventsManager->attach(
//     'application:beforeHandleRequest',
//      new App\Listeners\NotificationsListeners()
//       );


// ------------------------------------------------beforeHandleRequest end-----------------------------------

// ------------------------------------------------EventsManager container-----------------------------------

$container->set(
    'eventsManager',
    $eventsManager
);
// ------------------------------------------------EventsManager end-----------------------------------


$application->setEventsManager($eventsManager); //setting eventsManager

// ------------------------------------------------url container-----------------------------------

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

// ------------------------------------------------url end-----------------------------------


// ------------------------------------------------db container-----------------------------------

$container->set(
    'db',
    function () {
        $config = $this->getConfig();
        return new Mysql(
            [
                'host'     => $config->path('db.host'),
                'username' => $config->path('db.username'),
                'password' => $config->path('db.password'),
                'dbname'   => $config->path('db.dbname'),
                ]
        );
    }
);
// ------------------------------------------------db end-----------------------------------

// ------------------------------------------------logger container-----------------------------------

$container->set( 
    'mylogs',
    function() {
        $adapters = [
            "main"  => new \Phalcon\Logger\Adapter\Stream("../storage/log/main.log")
        ];
        $adapterFactory = new AdapterFactory();
        $loggerFactory  = new LoggerFactory($adapterFactory);
        
        return $loggerFactory->newInstance('prod-logger', $adapters);
    }, 
    true
 );
// ------------------------------------------------logger end-----------------------------------


// ------------------------------------------------Cache container-----------------------------------

 $container->set( 
    'Cache',
    function()
    {   
        $serializerFactory = new SerializerFactory();
        
        $options = [
            'defaultSerializer' => 'Php',
            'lifetime'          => 7200,
            'storageDir'        => BASE_PATH."/storage/cache"
        ];
        $adapter = new Phalcon\Cache\Adapter\Stream($serializerFactory, $options);
     $cache = new Cache($adapter);
     return $cache;
  }
);
// ------------------------------------------------Cache end-----------------------------------

// ------------------------------------------------session container-----------------------------------

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    },
    true
);
// ------------------------------------------------session end-----------------------------------


// ------------------------------------------------config container-----------------------------------

$container->set( 
    'config',
    function() {
    $fileName = '../app/etc/config.php';
    $factory  = new ConfigFactory();
    return $factory->newInstance('php', $fileName);
    }, 
    true
 );
// ------------------------------------------------config end-----------------------------------


// ------------------------------------------------cookie container-----------------------------------

$container->set( 
    "cookies", function () { 
       $cookies = new Cookies();  
       $cookies->useEncryption(false);  
       return $cookies; 
    } 
 );
// ------------------------------------------------cookie end-----------------------------------

// ------------------------------------------------mongo container-----------------------------------

$container->set(
    'mongo',
    function () {
        $mongo = new MongoClient();

        return $mongo->selectDB('phalt');
    },
    true
);
// ------------------------------------------------mongo end-----------------------------------



try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}