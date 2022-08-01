<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Calcutta',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'K0I9yOJPLBqbaam4IWrqtelfxp1m1zEXB04f5H6D',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
	        'class' => 'yii\rbac\DbManager',
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
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'ping'  =>  'site/ping',
                [
	                'class'         => 'yii\rest\UrlRule',
	                'controller'    => 'v1/user',
	                'pluralize'     => false,
	                'tokens' => [
		                '{id}'             => '<id:\d+>',
	                ],
	                'extraPatterns' => [
		                'OPTIONS {id}'                  =>  'options',
		                'POST login'                    =>  'login',
		                'OPTIONS login'                 =>  'options',
                        'POST logout'                   =>  'logout',
                        'OPTIONS logout'                =>  'options',
                        'POST register'                 =>  'register',
                        'OPTIONS register'              =>  'options',
                        'POST profile'                  =>  'profile',
                        'OPTIONS profile'               =>  'options',
                        'GET home'                      =>  'home',
                        'OPTIONS home'                  =>  'options',
                        'POST verifyotp'                =>  'verifyotp',
                        'OPTIONS verifyotp'             =>  'options',
                        'POST setpin'                   =>  'setpin',
                        'OPTIONS setpin'                =>  'options',
                        'GET getdefaultlang'            =>  'getdefaultlang',
                        'OPTIONS getdefaultlang'        =>  'options',
                        'POST langupdate'               =>  'langupdate',
                        'OPTIONS langupdate'            =>  'options',
                        'POST verifypin'                =>  'verifypin',
                        'OPTIONS verifypin'             =>  'options',
                        'POST checksupervisor'          =>  'checksupervisor',
                        'OPTIONS checksupervisor'       =>  'options',
                        'POST profileupdate'            =>  'profileupdate',
                        'OPTIONS profileupdate'         =>  'options',
                        'POST termsupdate'              =>  'termsupdate',
                        'OPTIONS termsupdate'           =>  'options',
                        'GET userdashboard'             =>  'userdashboard',
                        'OPTIONS userdashboard'         =>  'options',
                        'GET usertotalpoints'           =>  'usertotalpoints',
                        'OPTIONS usertotalpoints'       =>  'options',
                        'GET getstatelist'              =>  'getstatelist',
                        'OPTIONS getstatelist'          =>  'options',
                        'GET getcitylist'               =>  'getcitylist',
                        'OPTIONS getcitylist'           =>  'options',
                        'GET getcitylistbystate'        =>  'getcitylistbystate',
                        'OPTIONS getcitylistbystate'    =>  'options',
	                ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/common',
                    'pluralize'     => false,
                    'tokens' => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'            =>  'options',
                        'POST video'              =>  'video',
                        'OPTIONS video'           =>  'options',
                        'POST winners'            =>  'winners',
                        'OPTIONS winners'         =>  'options',

                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/reward',
                    'pluralize'     => false,
                    'tokens' => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                        =>  'options',
                        'GET categories'                      =>  'categories',
                        'OPTIONS categories'                  =>  'options',
                        'GET productlisthome'                 =>  'productlisthome',
                        'OPTIONS productlisthome'             =>  'options',
                        'GET productlistbycategory'           =>  'productlistbycategory',
                        'OPTIONS productlistbycategory'       =>  'options',
                        'GET productlistbysubcategory'        =>  'productlistbysubcategory',
                        'OPTIONS productlistbysubcategory'    =>  'options',
                        'GET productdetails'                  =>  'productdetails',
                        'OPTIONS productdetails'              =>  'options',
                        'POST savecart'                       =>  'savecart',
                        'OPTIONS savecart'                    =>  'options',
                        'GET viewcart'                        =>  'viewcart',
                        'OPTIONS viewcart'                    =>  'options',
                        'POST deletecartbyprod'               =>  'deletecartbyprod',
                        'OPTIONS deletecartbyprod'            =>  'options',
                        'GET checkout'                        =>  'checkout',
                        'OPTIONS checkout'                    =>  'options',
                        'POST addshippingaddress'             =>  'addshippingaddress',
                        'OPTIONS addshippingaddress'          =>  'options',
                        'GET getshippingaddbyid'              =>  'getshippingaddbyid',
                        'OPTIONS getshippingaddbyid'          =>  'options',
                        'PUT updateshippinadd'                =>  'updateshippinadd',
                        'OPTIONS updateshippinadd'            =>  'options',
                        'POST deleteshipadd'                  =>  'deleteshipadd',
                        'OPTIONS deleteshipadd'               =>  'options',
                        'GET orderconfirm'                    =>  'orderconfirm',
                        'OPTIONS orderconfirm'                =>  'options',
                        'POST orders'                         =>  'orders',
                        'OPTIONS orders'                      =>  'options',

                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/task',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                  =>  'options',
                        'GET tasklist'                  =>  'tasklist',
                        'OPTIONS tasklist'              =>  'options',
                        'GET tasklistdesc'              =>  'tasklistdesc',
                        'OPTIONS tasklistdesc'          =>  'options',
                        'GET getquizactivity'           =>  'getquizactivity',
                        'OPTIONS getquizactivity'       =>  'options',
                        'GET getsubtaskactivity'        =>  'getsubtaskactivity',
                        'OPTIONS getsubtaskactivity'    =>  'options',
                        'POST dotaskactivity'           =>  'dotaskactivity',
                        'OPTIONS dotaskactivity'        =>  'options',
                        'GET currentwof'                =>  'currentwof',
                        'OPTIONS currentwof'            =>  'options',
                        'GET wof'                       =>  'wof',
                        'OPTIONS wof'                   =>  'options',
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/admin',
                    'pluralize'     => false,
                    'tokens' => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'            =>  'options',
                        'POST login'              =>  'login',
                        'OPTIONS login'           =>  'options',
                        // National Team Dashboard API calls
                        'GET dashboard'           =>  'dashboard',
                        'OPTIONS dashboard'       =>  'options',
                        'GET regiondata'           =>  'regiondata',
                        'OPTIONS regiondata'       =>  'options',
                        'GET pograph'           =>  'pograph',
                        'OPTIONS pograph'       =>  'options',
                        'GET upgraph'           =>  'upgraph',
                        'OPTIONS upgraph'       =>  'options',
                        'GET rlbgraph'           =>  'rlbgraph',
                        'OPTIONS rlbgraph'       =>  'options',
                        'GET uegraph'           =>  'uegraph',
                        'OPTIONS uegraph'       =>  'options',
                        'GET rewardgraph'           =>  'rewardgraph',
                        'OPTIONS rewardgraph'       =>  'options',

                        // ASM Dashboard API calls
                        'GET asmdashboard'           =>  'asmdashboard',
                        'OPTIONS asmdashboard'       =>  'options',
                        'GET upgraphasm'           =>  'upgraphasm',
                        'OPTIONS upgraphasm'       =>  'options',
                        'GET uegraphasm'           =>  'uegraphasm',
                        'OPTIONS uegraphasm'       =>  'options',
                        'GET rlbgraphasm'           =>  'rlbgraphasm',
                        'OPTIONS rlbgraphasm'       =>  'options',
                        'GET rewardgraphasm'           =>  'rewardgraphasm',
                        'OPTIONS rewardgraphasm'       =>  'options',

                        // SH Dashboard API calls
                        'GET shdashboard'           =>  'shdashboard',
                        'OPTIONS shdashboard'       =>  'options',
                        'GET upgraphsh'           =>  'upgraphsh',
                        'OPTIONS upgraphsh'       =>  'options',
                        'GET uegraphsh'           =>  'uegraphsh',
                        'OPTIONS uegraphsh'       =>  'options',
                        'GET rlbgraphsh'           =>  'rlbgraphsh',
                        'OPTIONS rlbgraphsh'       =>  'options',
                        'GET rewardgraphsh'           =>  'rewardgraphsh',
                        'OPTIONS rewardgraphsh'       =>  'options',
                        'GET reportdata'           =>  'reportdata',
                        'OPTIONS reportdata'       =>  'options',

                        // SE Dashboard API calls
                        'GET sedashboard'           =>  'sedashboard',
                        'OPTIONS sedashboard'       =>  'options',
                        'GET pographse'           =>  'pographse',
                        'OPTIONS pographse'       =>  'options',
                        'GET upgraphse'           =>  'upgraphse',
                        'OPTIONS upgraphse'       =>  'options',
                        'GET rlbgraphse'           =>  'rlbgraphse',
                        'OPTIONS rlbgraphse'       =>  'options',
                        'GET uegraphse'           =>  'uegraphse',
                        'OPTIONS uegraphse'       =>  'options',
                        'GET rewardgraphse'           =>  'rewardgraphse',
                        'OPTIONS rewardgraphse'       =>  'options',
                        // Program 
                        'POST addprogram'         => 'addprogram',
                        'OPTIONS addprogram'      => 'options',
                        'GET program'             => 'program',
                        'OPTIONS program'          => 'options',
                        'POST searchprogram'       => 'searchprogram',
                        'OPTIONS searchprogram'    => 'options',
                        'POST searchactivation'       => 'searchactivation',
                        'OPTIONS searchactivation'    => 'options',
                        'GET programbyid'          => 'programbyid',
                        'OPTIONS programbyid'      => 'options',
                        'PUT updateprogram'        => 'updateprogram',
                        'OPTIONS updateprogram'    => 'options',
                        // GeoHierarchy
                        'GET geohierarchy'         => 'geohierarchy',
                        'OPTIONS geohierarchy'     => 'options',
                        'POST addgeohierarchy'     => 'addgeohierarchy',
                        'OPTIONS addgeohierarchy'  => 'options',
                        'GET region'               => 'region',
                        'OPTIONS region'        => 'options',
                        'POST addregion'        => 'addregion',
                        'OPTIONS addregion'     => 'options',
                        'POST searchregion'       => 'searchregion',
                        'OPTIONS searchregion'    => 'options',
                        'GET regionbyid'        => 'regionbyid',
                        'OPTIONS regionbyid'    => 'options',
                        'PUT updateregion'     => 'updateregion',
                        'OPTIONS updateregion'  => 'options',
                        'GET roles'             => 'roles',
                        'OPTIONS roles'         => 'options',
                        'POST searchuserroles'  => 'searchuserroles',
                        'OPTIONS searchuserroles'=> 'options',
                        'GET userrolesbyid'     => 'userrolesbyid',
                        'OPTIONS userrolesbyid' => 'options',
                        'POST adduserrole'      => 'adduserrole',
                        'OPTIONS adduserrole'   => 'options',
                        'POST updateuserrole'    => 'updateuserrole',
                        'OPTIONS updateuserrole' => 'options',
                        'GET getusers'          => 'getusers',
                        'OPTIONS getusers'      => 'options',
                        'POST searchuser'       => 'searchuser',
                        'OPTIONS searchuser'    => 'options',
                        'POST adduser'          => 'adduser',
                        'OPTIONS adduser'       => 'options',
                        'GET userbyid'          => 'userbyid',
                        'OPTIONS userbyid'      => 'options',
                        'PUT updateuser'        => 'updateuser',
                        'OPTIONS updateuser'    => 'options',
                        'GET campaigns'         => 'campaigns',
                        'OPTIONS campaigns'     => 'options',
                        'POST addcampaign'      => 'addcampaign',
                        'OPTIONS addcampaign'   => 'options',
                        'GET campaignbyid'      => 'campaignbyid',
                        'OPTIONS campaignbyid'  => 'options',
                        'PUT updatecampaign'    => 'updatecampaign',
                        'OPTIONS updatecampaign' => 'options',
                        'GET campaignactivation' => 'campaignactivation',
                        'OPTIONS campaignactivation'=> 'options',
                        'POST addcampaignactivation' => 'addcampaignactivation',
                        'OPTIONS addcampaignactivation'=> 'options',
                        'GET campaignactivationbyid' => 'campaignactivationbyid',
                        'OPTIONS campaignactivationbyid'=> 'options',
                        'PUT updatecampaignactivation'    => 'updatecampaignactivation',
                        'OPTIONS updatecampaignactivation' => 'options',
                        'POST searchcampaign'   => 'searchcampaign',
                        'OPTIONS searchcampaign' => 'options',
                        'GET campaignenrollment' => 'campaignenrollment',
                        'OPTIONS campaignenrollment' => 'options',
                        'GET campaignenrolmentbyid'=> 'campaignenrolmentbyid',
                        'OPTIONS campaignenrolmentbyid'=> 'options',
                        'POST addcampaignenrolment'=> 'addcampaignenrolment',
                        'OPTIONS addcampaignenrolment' => 'options',
                        'PUT updatecampaignenrolment' => 'updatecampaignenrolment',
                        'OPTIONS updatecampaignenrolment'=> 'options',
                        'GET campaignengagements'=> 'campaignengagements',
                        'OPTIONS campaignengagements'=> 'options',
                        'POST addcampaignengagement' => 'addcampaignengagement',
                        'OPTIONS addcampaignengagement'=> 'options',
                        'GET campaignengagementbyid'=> 'campaignengagementbyid',
                        'OPTIONS campaignengagementbyid'=> 'options',
                        'PUT updatecampaignengagement'=> 'updatecampaignengagement',
                        'OPTIONS updatecampaignengagement'=> 'options',
                        'GET campaignactivities'=> 'campaignactivities',
                        'OPTIONS campaignactivities'=> 'options',
                        'POST searchenrolment'=> 'searchenrolment',
                        'OPTIONS searchenrolment'=> 'options',
                        'POST searchengagement'=>'searchengagement',
                        'OPTIONS searchengagement'=> 'options',
                        'POST addcampaignactivites'=> 'addcampaignactivites',
                        'OPTIONS addcampaignactivites'=> 'options',
                        'GET campaignactivitybyid'=> 'campaignactivitybyid',
                        'OPTIONS campaignactivitybyid'=>'options',
                        'PUT updatecampaignactivity'=> 'updatecampaignactivity',
                        'OPTIONS updatecampaignactivity'=> 'options',
                        'POST searchactivites'=> 'searchactivites',
                        'OPTIONS searchactivites'=> 'options',
                        'GET campaignmodules'=> 'campaignmodules',
                        'OPTIONS campaignmodules'=> 'options',
                        'POST addcampaignmodules'=> 'addcampaignmodules',
                        'OPTIONS addcampaignmodules'=> 'options',
                        'PUT updatecampaignmodule'=> 'updatecampaignmodule',
                        'OPTIONS updatecampaignmodule'=> 'options',
                        'GET campaignmodulebyid'=> 'campaignmodulebyid',
                        'OPTIONS campaignmodulebyid'=> 'options',
                        'GET campaignroleaccess'=> 'campaignroleaccess',
                        'OPTIONS campaignroleaccess'=> 'options',
                        'POST addcampaignroleaccess'=> 'addcampaignroleaccess',
                        'OPTIONS addcampaignroleaccess'=> 'options',
                        'GET campaignroleaccessbyid'=> 'campaignroleaccessbyid',
                        'OPTIONS campaignroleaccessbyid'=> 'options',
                        'PUT updatecampaignroleaccess'=> 'updatecampaignroleaccess',
                        'OPTIONS updatecampaignroleaccess'=> 'options',
                        'GET campaignquizactivation'=> 'campaignquizactivation',
                        'OPTIONS campaignquizactivation'=> 'options',
                        'POST addcampaignquizactivity'=>'addcampaignquizactivity',
                        'OPTIONS addcampaignquizactivity'=> 'options',
                        'PUT updatecampaignquizactivity'=>'updatecampaignquizactivity',
                        'OPTIONS updatecampaignquizactivity'=>'options',
                        'GET campaignquizactivitybyid'=>'campaignquizactivitybyid',
                        'OPTIONS campaignquizactivitybyid'=>'options',
                        
                    ]   
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/tracking',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                  =>  'options',
                        'GET salesrevenuedashboard'     =>'salesrevenuedashboard',
                        'OPTIONS salesrevenuedashboard' =>'options',
                        'POST logyoursales'             =>  'logyoursales',
                        'OPTIONS logyoursales'          =>  'options',
                        'GET pendingvisits'             =>  'pendingvisits',
                        'OPTIONS pendingvisits'         =>  'options',
                        
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/pointofsale',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                     =>  'options',
                        'GET region'                       =>  'region',
                        'OPTIONS region'                   =>  'options',
                        'GET storelist'                    =>  'storelist',
                        'OPTIONS storelist'                =>  'options',
                        'GET posmauditlist'                =>  'posmauditlist',
                        'OPTIONS posmauditlist'            =>  'options',
                        'POST posmtrans'                   =>  'posmtrans',
                        'OPTIONS posmtrans'                =>  'options',
                        'POST qrcodegenerationapi'          =>  'qrcodegenerationapi',
                        'OPTIONS qrcodegenerationapi'       =>  'options',
                    ]
                ],
				[
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/enrollment',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                => 'options',
                        'GET enrollmentdashboard'     => 'enrollmentdashboard',
                        'OPTIONS enrollmentdashboard' => 'options',                        
                        'POST userenrollment'         => 'userenrollment',
                        'OPTIONS userenrollment'      => 'options',
                        'OPTIONS pendingvisits'       => 'options',
                        'GET pendingvisits'           => 'pendingvisits',
                        'OPTIONS updateoutlet'        => 'options',
                        'POST updateoutlet'           => 'updateoutlet',
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/hub-admin',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}'                => 'options',
                        'POST assignmysteryshopper'   => 'assignmysteryshopper',
                        'OPTIONS assignmysteryshopper' => 'options',
                        'PUT updatemysteryassignment'  => 'updatemysteryassignment',
                        'OPTIONS updatemysteryassignment' => 'options'
                    ]
                ],
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {

                $response = $event->sender;
                if($response->format == 'html') {
                    return $response;
                }

                $responseData = $response->data;

                if(is_string($responseData) && json_decode($responseData)) {
                    $responseData = json_decode($responseData, true);
                }


                if($response->statusCode >= 200 && $response->statusCode <= 299) {
                    $response->data = [
                        'success'   => true,
                        'status'    => $response->statusCode,
                        'data'      => $responseData,
                    ];
                } else {
                    $response->data = [
                        'success'   => false,
                        'status'    => $response->statusCode,
                        'data'      => $responseData,
                    ];

                }
                return $response;
            },
        ],
        /*'sse' => [
	        'class' => \odannyc\Yii2SSE\LibSSE::class
        ]*/

    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
