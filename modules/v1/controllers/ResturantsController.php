<?php

namespace app\modules\v1\controllers;


use app\filters\auth\HttpBearerAuth;

use Yii;
//use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\web\HttpException;
use app\models\State;
use app\models\User;
use app\models\Customer;
use app\models\Classification;
use app\models\Offer;
use app\models\Restaurant_offer;
use app\models\Restaurant;
use app\models\Customer_login_history;

class ResturantsController extends ActiveController
{
    public $modelClass = 'app\models\State';

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
                HttpBearerAuth::className(),
            ],

        ];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::className(),
            'actions' => [
                'list' => ['get'],
                'liststate' => ['get'],
                'nearbyrestaurants' => ['post'],
                'login' => ['post'],
                'verifyotp' => ['post'],
                'restaurant-details' => ['post'],
                'offer-details' => ['post']
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
        $behaviors['authenticator']['except'] = ['options', 'list', 'liststate', 'create-state', 'update-state', 'delete-state', 'import-state', 'get-state-asc', 'nearbyrestaurants', 'login', 'verifyotp','restaurant-details','offer-details'];

        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'List', 'restaurant-details', 'offer-details'],
                    'roles' => ['admin', 'manageUsers'],
                ],
                [
                    'allow' => true,
                    'actions' => ['me'],
                    'roles' => ['user'],
                ],
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

    public function actionOptions($id = null)
    {
        return "ok";
    }

    public function getBearerAccessToken()
    {
        $bearer = null;
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $matches = array();
            preg_match('/^Bearer\s+(.*?)$/', $headers['Authorization'], $matches);
            if (isset($matches[1])) {
                $bearer = $matches[1];
            }
        } elseif (isset($headers['authorization'])) {
            $matches = array();
            preg_match('/^Bearer\s+(.*?)$/', $headers['authorization'], $matches);
            if (isset($matches[1])) {
                $bearer = $matches[1];
            }
        }
        return $bearer;
    }

    public function actionNearbyrestaurants()
    {
        $latitude = Yii::$app->request->post('latitude');
        $longitude = Yii::$app->request->post('longitude');
        //$range = '';
        //$page = '';
        $objResturentOffer = new Restaurant_offer();
        $resListRestaurants = $objResturentOffer->getNearByResrestaurants($latitude, $longitude);
        $rest_details = array();
        if (!empty($resListRestaurants)) {
            foreach ($resListRestaurants as $val) {
                $rest_Id = $val['id'];
                $resResturantOffer = $objResturentOffer->getResturantOffers($rest_Id);
                if (!empty($resResturantOffer)) {
                    $val["offers"] = $resResturantOffer;
                    array_push($rest_details, $val);
                } else {
                    $val["offers"] = "No offers found";
                    array_push($rest_details, $val);
                }
            }
            return $rest_details;
        } else {
            $this->throwException(411, "Thier is no restaurants near by you");
        }
    }

    public function actionRestaurantDetails(){
        $restaurant_id = Yii::$app->request->post('restaurant_id');
        $rest_details = array();
        $objResturentOffer = new Restaurant_offer();
        $resListRestaurants = $objResturentOffer->getRestaurantDetails($restaurant_id);
        if(!empty($resListRestaurants) || $resListRestaurants != ''){
            $resResturantOffer = $objResturentOffer->getResturantOffers($restaurant_id);
            if (!empty($resResturantOffer)) {
                $resListRestaurants["offers"] = $resResturantOffer;
                array_push($rest_details, $resListRestaurants);
            } else {
                $resListRestaurants["offers"] = "No offers found";
                array_push($rest_details, $resListRestaurants);
            }
            return $rest_details;
        }else{
            $this->throwException(422, "Restaurant unavailable!!");
        }
    }

    public function actionOfferDetails(){
        $restaurant_offer_id = Yii::$app->request->post('restaurant_offer_id');

        $objResturentOffer = new Restaurant_offer();
        $resListRestaurants = $objResturentOffer->getOfferDetails($restaurant_offer_id);
        return $resListRestaurants;
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

    // public function validateLatLong($latitude, $longitude)
    // {
    //     // $lat_pattern  = '/\A[+-]?(?:90(?:\.0{1,18})?|\d(?(?<=9)|\d?)\.\d{1,18})\z/x';
    //     // $long_pattern = '/\A[+-]?(?:180(?:\.0{1,18})?|(?:1[0-7]\d|\d{1,2})\.\d{1,18})\z/x';
    //     $lat_pattern = '/^[+-]?(90|[1-8][0-9][.][0-9]{1,20}|[0-9][.][0-9]{1,20})$/';
    //     $long_pattern = '/^-?(180|1[1-7][0-9][.][0-9]{1,20}|[1-9][0-9][.][0-9]{1,20}|[0-9][.][0-9]{1,20})$/';
    //     if (preg_match($lat_pattern, $lat) == 1 && preg_match($long_pattern, $long) == 1) {
    //         return 1;
    //     }
    // }
}
