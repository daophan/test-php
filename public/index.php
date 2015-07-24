<?php

mb_internal_encoding('utf-8');

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as Session;


try {

    // Register an autoloader
    $loader = new Loader();
    $loader->registerDirs(array(
        '../app/controllers/',
        '../app/models/'
        ))->register();

    // Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    // Setup the view component
    $di->set('view', function(){
        $view = new View();
        $view->setViewsDir('../app/views/');
        return $view;
    });

    // Setup a base URI so that all generated URIs include the "tutorial" folder
    $di->set('url', function(){
        $url = new UrlProvider();
        $url->setBaseUri('/test-php/');
        return $url;
    });

    $di->set('cookies', function() {
        $cookies = new Phalcon\Http\Response\Cookies();
        $cookies->useEncryption(false);
        return $cookies;
    });


    //Start the session the first time when some component request the session service
    $di->setShared('session', function() {
        $session = new Session();
        $session->start();
        return $session;
    });

    $di->set('security', function(){
        $security = new Phalcon\Security();
        //Set the password hashing factor to 12 rounds
        $security->setWorkFactor(12);
        return $security;
    }, true);

    $di->set('db', function() {
        try {
            $db = new \Phalcon\Db\Adapter\Pdo\Mysql(
                array(
                    "host" => 'localhost',
                    "username" => 'root',
                    "password" => '',
                    "dbname" => 'test-php'
                )
            );
        } catch (Exception $e) {
            die("<b>Error when initializing database connection:</b> " . $e->getMessage());
        }
        
        return $db;
    });

    // Handle the request
    $application = new Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
   echo "PhalconException: ", $e->getMessage();
}
