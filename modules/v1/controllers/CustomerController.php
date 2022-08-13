<?php

namespace app\modules\v1\controllers;


use app\filters\auth\HttpBearerAuth;

use Yii;
//use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\web\HttpException;
use app\helpers\AppHelper;
use app\models\Message;
use app\models\State;
use app\models\User;
use app\models\Customer;
use app\models\Customer_login_history;
use app\models\Customer_wishlist;

class CustomerController extends ActiveController
{
    public $modelClass = 'app\models\Customer';

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
                'login' => ['post'],
                'verifyotp' => ['post'],
                'editprofile' => ['post'],
                'toggle-wishlist' => ['post'],
                'redeem-offer' => ['post'],
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
        $behaviors['authenticator']['except'] = ['options', 'create-state', 'update-state', 'delete-state', 'import-state', 'get-state-asc', 'login', 'verifyotp', 'editprofile', 'toggle-wishlist','redeem-offer'];

        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'List'],
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

    public function actionLogin()
    {
        $pho_no = Yii::$app->request->post('pho_no');

        if (preg_match("/^[0-9]{10}$/", $pho_no)) {
            
            $objCustomer = new Customer();
            $objCustomer_login_history = new Customer_login_history();
            $resCustomer = $objCustomer->find()->where(['mobile_no' => $pho_no])->one();

            $message     = new Message();
           // $checkOTP = $message->otpWithinTwoMins($pho_no);

            if (($resCustomer || !empty($resCustomer)) && $resCustomer['status'] == 1) {
                $id = $resCustomer['id'];
                $resCustomer->updated_date = date('Y-m-d H:i:s');
                $resCustomer->save();
            }elseif(($resCustomer || !empty($resCustomer)) && $resCustomer['status'] == 0){
                throw new HttpException(422, "Contact Number is blocked. Contact 9999900001 to activate!");
            } else {
                $objCustomer->mobile_no = $pho_no;
                $objCustomer->created_date = date('Y-d-m H:i:s');
                $objCustomer->updated_date = date('Y-d-m H:i:s');
                $objCustomer->save();
                $id = Yii::$app->db->getLastInsertID();
            }
            $getOTP = $this->generateOTP($pho_no);

            $objCustomer_login_history->customer_id = $id;
            $objCustomer_login_history->phone_number = $pho_no;
            $objCustomer_login_history->otp = $getOTP;
            if($objCustomer_login_history->save()){
                $responseData = [
                    'customer_id' => $id,
                    'mobile_no'   => $pho_no,
                    //'otp'         => md5($getOTP),
                    'otp'         => $getOTP,

                ];
            }
            
            return $responseData;
        } else {
            $this->throwException(411, "Invalid Contact Number");
        }
    }

    private function generateOTP($mobile){
        if(isset($mobile)){
            if($mobile == '9999900001'){
                $otp = '4321';
            }
            else{
                $date           = date('Y-m-d H:i:s');
                $appHelper      = new AppHelper();
                $otp            = $appHelper->getUniqueRandomNum();
                $arrParams      = array(
                    array('name' => 'otp', 'value' => $otp),
                );
                $msg         = "Your OTP is ".$otp.". Team BigCity";
                $message     = new Message();
                $message->sendSMS($mobile,$msg,'Login SMS','gen_otp_sms');
            }
            return $otp;
        }
        else {
            // Validation error
            throw new HttpException(422, json_encode("Permission denied."));
        }
    }

    public function actionVerifyotp()
    {
        $appHelper  = new AppHelper();
        $date       = date('Y-m-d H:i:s');
        if(Yii::$app->request->post('customer_id') && Yii::$app->request->post('otp')){
            $customer_id = Yii::$app->request->post('customer_id');
            $otp         = Yii::$app->request->post('otp');

            $customerLogin = new Customer_login_history();
            $logindetails  = $customerLogin->verifyOTP($customer_id,$otp);

            if(isset($logindetails['id']) && !empty($logindetails['id'])){
                $customerModel      = new Customer();
                $token_expiry   = date('Y-m-d H:i:s', strtotime("+30 days", strtotime($date)));
                $customerModel->generateAccessToken();
                $updateCustomers = Customer::updateAll(['access_token' =>$customerModel->access_token,'access_token_expiry'=>$token_expiry, 'updated_date' => $date], ['id' => $customer_id]);

                $ipAddress= Yii::$app->request->userIP;
                $updateCustomerLogin = Customer_login_history::updateAll(['access_token' =>$customerModel->access_token,'status'=> 1, 'ip_address' => $ipAddress, 'loginat' => $date], ['customer_id' => $customer_id, 'otp' => $otp]);
                $responseData = [
                    'customer_id'           => $customer_id,
                    'customer_name'         => $logindetails['customer_name'] != '' ? $logindetails['customer_name'] : '',
                    'customer_phone_number' => $logindetails['phone_number'] != '' ? $logindetails['phone_number'] : '',
                    'access_token'          => $customerModel->access_token,
                    'role'                  => 'customer'
                ];
            }else{
                $responseData = [
                    'verified'     => false,
                    'message'      => 'Please enter correct OTP to proceed',
                ];
            }
            return $responseData;
        }
        else{
            throw new HttpException(422, json_encode("Customer ID & OTP both are required!!."));
        }

    }

    public function actionEditprofile()
    {
        $pho_no = Yii::$app->request->post('pho_no');
        $objCustomer = new Customer();
        $token = $this->getBearerAccessToken();
        if (isset($token)) {
            $customerDetails = $objCustomer->getCustomerdetails($token, $pho_no);
            $access_token_expriy = $customerDetails['access_token_expiry'];
            if ($access_token_expriy != null || $access_token_expriy > date('Y-m-d H:i:s')) {
                $address = Yii::$app->request->post('address');
                $email = Yii::$app->request->post('email');
                $customer_name = Yii::$app->request->post('customer_name');
                $dob = Yii::$app->request->post('dob');
                $city = Yii::$app->request->post('city');
                $pincode = Yii::$app->request->post('pincode');
                $gender = Yii::$app->request->post('gender');
                $state_id = Yii::$app->request->post('state_id');
                $resCustomer = $objCustomer->editCustomerProfile($objCustomer, $pho_no, $address, $email, $customer_name, $dob, $city, $pincode, $gender, $state_id);
                return   $resCustomer;
            } else {
                $this->throwException(401, 'Unauthorized user access!');
            }
        } else {
            $this->throwException(422, 'The requested access_token could not be found.');
        }
    }

    public function actionToggleWishlist()
    {
        $objCustomer = new Customer();
        $token = $this->getBearerAccessToken(); 
        if (isset($token)) {
            $customer_id = Yii::$app->request->post('customer_id');
            $customerDetails = $objCustomer->getCustomerdetails($token, $customer_id);

            $access_token_expriy = $customerDetails['access_token_expiry'];
            if ($access_token_expriy != null || $access_token_expriy > date('Y-m-d H:i:s')) {
                $restaurant_offer_id = Yii::$app->request->post('restaurant_offer_id');
                $objWishlist = new Customer_wishlist();

                $checkWishlist = $objWishlist->check_whitelist($customer_id,$restaurant_offer_id);

                //check whether whishlist is existing 
                if(!empty($checkWishlist) || $checkWishlist != ''){
                    $delWishList = Customer_wishlist::find()
                            ->where(['customer_id'=>$customer_id])
                            ->andwhere(['restaurant_offer_id'=>$restaurant_offer_id])
                            ->one()
                            ->delete();
                    if(isset($delWishList)){
                        $responseData = [
                            'customer_id'  => $customer_id,
                            'message'      => 'Removed from favourites!',
                            'status'       => 'inactive'
                        ];
                    }
                }else{
                    $objWishlist->customer_id=$customer_id;
                    $objWishlist->restaurant_offer_id=$restaurant_offer_id;
                    $objWishlist->created_date=date('Y-m-d H:i:s');
                    if($objWishlist->save()){
                        $responseData = [
                            'whistlist_id' => Yii::$app->db->getLastInsertID(),
                            'customer_id'  => $customer_id,
                            'message'      => 'Your offer is saved in your favourites for future usage!',
                            'status'       => 'active'
                        ];
                    }
                }
                return $responseData;

            } else {
                $this->throwException(401, 'Unauthorized user access!');
            }
        } else {
            $this->throwException(411, 'The requested access_token could not be found.');
        }
    }

    public function actionRedeemOffer(){
        $pho_no = Yii::$app->request->post('pho_no');
        $objCustomer = new Customer();
        $token = $this->getBearerAccessToken();
        if (isset($token)) {
            $customerDetails = $objCustomer->getCustomerdetails($token, $pho_no);
            $access_token_expriy = $customerDetails['access_token_expiry'];
            if ($access_token_expriy != null || $access_token_expriy > date('Y-m-d H:i:s')) {
                //1. User will scan the qr code, unique_code will be sent as post data 
            } else {
                $this->throwException(401, 'Unauthorized user access!');
            }
        } else {
            $this->throwException(422, 'The requested access_token could not be found.');
        }
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
}
