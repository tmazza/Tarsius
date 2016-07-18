<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'defaultController' => 'site',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
    ),
    'modules' => array(
      'gii' => array(
          'class' => 'system.gii.GiiModule',
          'password' => 'bdg',
          'ipFilters' => array('*'),
      ),
    ),
    'language' => 'pt_br',
    'components' => array(
        'urlManager'  => require(dirname(__FILE__) . '/rotas.php'),
        'db'          => require(dirname(__FILE__) . '/database.php'),
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
    'params' => array(),
);
