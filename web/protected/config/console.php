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
        'db'          => require(dirname(__FILE__) . '/database.php'),
        'errorHandler' => array(
            'errorAction' => '/site/error',
        ),
    ),
);
