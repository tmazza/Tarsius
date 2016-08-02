<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'defaultController' => 'site',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.controllers.*',
    ),
    'modules' => array(
      'gii' => array(
          'class' => 'system.gii.GiiModule',
          'password' => 'gii',
          'ipFilters' => array('*'),
      ),
    ),
    'language' => 'pt_br',
    'components' => array(
        'urlManager'  => require __DIR__ . '/rotas.php',
        'db'          => require __DIR__ . '/database.php',
        'dbExport'    => require __DIR__ . '/dbExport.php',
        'errorHandler' => array(
            'errorAction' => '/site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error',
                    'logFile' => 'error',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'warning',
                    'logFile' => 'warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace',
                    'logFile' => 'trace',
                ),
            ),
        ),
    ),
    'params' => [
        'templatesDir'=>__DIR__.'/../../../data/template'
    ],
);
