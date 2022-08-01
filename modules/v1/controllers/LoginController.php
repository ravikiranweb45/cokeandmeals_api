<?php

namespace app\modules\v1\controllers;
use app\filters\auth\HttpBearerAuth;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\helpers\Json;
use app\models\LoginForm;

class LoginController extends ActiveController
{
    public $modelClass = 'app\models\Region';

        public function __construct($id, $module, $config = [])
        {
            parent::__construct($id, $module, $config);

        }

        public function actions()
        {
            return [];
        }

        public function behaviors()
        {
            $behaviors = parent::behaviors();

            $behaviors['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    //HttpBearerAuth::className(),
                ],

            ];

            $behaviors['verbs'] = [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                ],
            ];

            // remove authentication filter
            $auth = $behaviors['authenticator'];
            unset($behaviors['authenticator']);

            // add CORS filter
            $behaviors['corsFilter'] = [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Allow-Origin' => ['*'],
                ],
            ];

            // re-add authentication filter
            $behaviors['authenticator'] = $auth;
            // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
            $behaviors['authenticator']['except'] = ['options', 'login'];


	        // setup access
	        $behaviors['access'] = [
		        'class' => AccessControl::className(),
		        'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
		        'rules' => [
			        [
				        'allow' => true,
				        'actions' => ['index', 'view', 'create', 'update', 'delete','login'],
				        'roles' => ['admin', 'manageUsers'],
			        ],
			        [
			            'allow' => true,
			            'actions'   => ['me'],
			            'roles' => ['user']
			        ]
		        ],
	        ];

            return $behaviors;
        }
        public function auth()
        {
            return [
                'bearerAuth' => [
                    'class' => \yii\filters\auth\HttpBearerAuth::className(),
                ],
            ];
        }

        public function actionOptions($id = null) {
            return "ok";
        }

    /**
     * Generic function to throw HttpExceptions
     * @param $errCode
     * @param $errMsg
     * @author Suresh N
     */
    private function throwException($errCode, $errMsg)
    {
        throw new \yii\web\HttpException($errCode, $errMsg);
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->roles = [
            User::ROLE_ADMIN,
            User::ROLE_SUPERADMIN,
            User::ROLE_HUBADMIN,
            User::ROLE_TEAMLEADER,
            User::ROLE_AGENT,
            User::ROLE_AGENCYTEAMLEADER,
            User::ROLE_AGENCYAGENT,
            User::ROLE_USER,
            User::ROLE_AGENTCALLCENTER,
            User::ROLE_DASHBOARDADMIN,
            User::ROLE_MYSTERYAGENT,
			User::ROLE_REWARDUSER
        ];
        // Requires Login Form Objects
        $model->login();

        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            $user = $model->getUser();
            $user->generateAccessTokenAfterUpdatingClientInfo();
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(200);
            $id = implode(',', array_values($user->getPrimaryKey(true)));
            $user_role = User::findIdentity($id);

            $responseData = [
                'id' => (int) $id,
                'access_token' => $user->device_token,
                'user_name' => $user->username,
                'user_role' => $user->user_role_id,
            ];

            return $responseData;

        } else {

            $this->throwException(422, "Username Or Password is not matching");
        }
    }
}
