<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Jakarta',
    // 'on beforeAction' => function($event){
    //     $action = $event->action;
    //     $moduleID = $action->controller->module->id;
    //     $controllerID = $action->controller->id;
    //     $actionID = $action->id;
    //     $user = \Yii::$app->user;
    //     $userID = $user->id;
    //     if(!in_array($controllerID,['default','site'])){
    //         $auth = \app\models\Auth::find()
    //             ->where([
    //                 'module' => $moduleID,
    //                 'controller' => $controllerID,
    //                 'action' => $actionID,
    //                 'user_id' => $userID,
    //             ])
    //             ->count();
    //         if($auth==0) {
    //             if (!$action instanceof \yii\web\ErrorAction) {
    //                 if ($user->getIsGuest()) {
    //                     $user->loginRequired();
    //                 } else {
    //                     throw new \yii\web\ForbiddenHttpException('Anda tidak diizinkan untuk mengakses halaman ' . $action->id . ' ini!');
    //                 }
    //             }
    //         }    
    //     }
    // },
//     'as access' => [
//         'class' => '\hscstudio\mimin\components\AccessControl',
//         'allowActions' => [
//            // add wildcard allowed action here!
//            'site/*',
//            'debug/*',
//            'mimin/*', // only in dev mode
//        ],
//    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'mimin' => [
            'class' => '\hscstudio\mimin\Module',
        ],
    
    ],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'kYWJ9XF7_iVrxgSK_nzQhfRYOVcuoxZk',
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',

        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '1108738619578913',
                    'clientSecret' => '879b1658b2199ca8ff2521a3ac85b4a8',
                ],
            ],
        ],
    
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' =>[
            'enablePrettyUrl' =>true,
            'showScriptName' =>false,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
