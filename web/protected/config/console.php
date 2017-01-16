<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.controllers.*',
    ),
    'language' => 'pt_br',
    'components' => array(
        'db'          => require __DIR__ . '/database.php',
        'errorHandler' => array(
            'errorAction' => '/site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'consoleError',
                    'logFile' => 'error',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'consoleWarning',
                    'logFile' => 'warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'tarsius',
                    'categories'=>'tarsius.*',
                ),
            ),
        ),
    ),
    'params' => [
        'templatesDir' => __DIR__. '/../../../data/template',
        'runtimeDir' => __DIR__. '/../../../data/runtime',
        'urlBase' => '',
    ],
);
