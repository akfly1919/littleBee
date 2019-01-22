<?php
// 自定义参数
$params = array_merge(
    require __DIR__ . '/params.php'
);

// 接口
$interface = array_merge(
    require __DIR__ . '/appInterface.php',
    require __DIR__ . '/miniProgramInterface.php',
    require __DIR__ . '/commonInterface.php'
);

// 数据库配置
$db = require __DIR__ . '/db.php';

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => [
        'log'
    ],
    'timezone' => 'PRC',
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api'
        ],
        // db 修改
        'db' => $db,
        'user' => [
            'identityClass' => 'api\models\Team',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null,
            'identityCookie' => [
                'name' => '_identity-api',
                'httpOnly' => true
            ]
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-api'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ]
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'api/error'
        ],
        
        // 返回值格式化
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->data = [
                    'code' => $response->getStatusCode(),
                    'data' => $response->data,
                    'message' => $response->statusText
                ];
                
                $response->format = yii\web\Response::FORMAT_JSON;
            }
        ],
        
        // url管理器
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api',
                    'extraPatterns' => $interface
                ]
            ]
        ]
    ],
    'params' => $params
];
