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
use app\models\Customer_login_history;

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
                'login'=>['post'],
                'verifyotp'=>['post'],
                'editprofile'=>['post'],
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
        $behaviors['authenticator']['except'] = ['options', 'create-state', 'update-state', 'delete-state','import-state', 'get-state-asc','login','verifyotp','editprofile'];

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

   

    public function actionLogin(){
        $pho_no=Yii::$app->request->post('pho_no');
        if(preg_match("/^[0-9]{10}$/",$pho_no)){
            $getOTP= random_int(100000, 999999);          
           $objCustomer=new Customer();
           $objCustomer_login_history=new Customer_login_history();
           $resCustomer=$objCustomer->find()->where(['mobile_no'=>$pho_no])->andWhere(['status'=>1])->one();
           if($resCustomer || $resCustomer !=""){
                $id=$resCustomer['id'];
               $resCustomer->updated_date=date('Y-m-d H:i:s');
               $resCustomer->save();
               $resCustomer_login_history=$objCustomer_login_history->find()->where(['phone_number'=>$pho_no])->andWhere(['customer_id'=>$id])->one();
               $resCustomer_login_history->customer_id=$id;
               $resCustomer_login_history->phone_number=$pho_no;
               $resCustomer_login_history->otp=$getOTP;
              $resCustomer_login_history->save();
              return $getOTP;
           }else{
                $objCustomer->mobile_no=$pho_no;
                $objCustomer->created_date=date('Y-d-m H:i:s');
                $objCustomer->updated_date=date('Y-d-m H:i:s');
                $objCustomer->save();
                 $id= Yii::$app->db->getLastInsertID();
                 $objCustomer_login_history->customer_id=$id;
                 $objCustomer_login_history->phone_number=$pho_no;
                 $objCustomer_login_history->otp=$getOTP;
                 $objCustomer_login_history->save();
                 return $getOTP;
           }
          
        }else{
            $this->throwException(411,"Invalid Mobile Number");
        }
    }

    public function actionVerifyotp(){
        $otp=Yii::$app->request->post('otp');
        $pho_no=Yii::$app->request->post('pho_no');
        $objCustomer_login_history=new Customer_login_history();
       $resCustomer_login_history=$objCustomer_login_history->find()->where(['otp'=>$otp])->andWhere(['phone_number'=>$pho_no])->one();
       if($resCustomer_login_history || $resCustomer_login_history!=""){
        $c_id=$resCustomer_login_history['customer_id'];
        $usrmodel = new User();
        $usrmodel->generateAccessToken();                                                     
        $resCustomer_login_history->loginat=date('Y-d-m H:i:s');
        $resCustomer_login_history->status=1;
        $resCustomer_login_history->access_token=$usrmodel->access_token;
        $resCustomer_login_history->ip_address=Yii::$app->request->userIP;;
      $resCustomer_login_history->save();

       $resCustomer=Customer::find()->where(['mobile_no'=>$pho_no])->andWhere(['id'=>$c_id])->one();
       if($resCustomer || $resCustomer!=""){
          $resCustomer->created_date=date('Y-d-m H:i:s');
          $resCustomer->updated_date=date('Y-d-m H:i:s');
          $resCustomer->access_token=$usrmodel->access_token;
          $resCustomer->access_token_expiry=$usrmodel->access_token_expired_at;
         $resCustomer->save();
       }
       return $resCustomer;
       }else{
              $this->throwException(411,"Invalid OTP");
       }
    }
  
  public function actionEditprofile(){
    $pho_no=Yii::$app->request->post('pho_no');
    $objCustomer=new Customer();
   $token= $objCustomer->getBearerAccessToken();
    if(isset($token)){
        $customerDetails=$objCustomer->getCustomerdetails($token,$pho_no);
        $access_token_expriy=$customerDetails['access_token_expiry'];
            if($access_token_expriy !=null || $access_token_expriy>date('Y-m-d H:i:s')){
                $address=Yii::$app->request->post('address');
                $email=Yii::$app->request->post('email'); 
                $customer_name=Yii::$app->request->post('customer_name'); 
                $dob=Yii::$app->request->post('dob'); 
                $city=Yii::$app->request->post('city'); 
                $pincode=Yii::$app->request->post('pincode'); 
                $gender=Yii::$app->request->post('gender');  
                $state_id=Yii::$app->request->post('state_id'); 
                $resCustomer=$objCustomer->editCustomerProfile($objCustomer,$pho_no,$address,$email,$customer_name,$dob,$city,$pincode,$gender,$state_id);
                
                // $resCustomer=$objCustomer->find()->where(['mobile_no'=>$pho_no])->one();
                // $resCustomer->email=$email;
                // $resCustomer->customer_name=$customer_name;
                // $resCustomer->address=$address;
                // $resCustomer->mobile_no=$pho_no;
                // $resCustomer->dob=$dob;
                // $resCustomer->city=$city;
                // $resCustomer->pincode=$pincode;
                // $resCustomer->gender=$gender;
                // $resCustomer->state_id=$state_id;
                // $resCustomer->save();
                return   $resCustomer;
            }else{
                $this->throwException(401, 'Unauthorized user access!');
            }
    }else{
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
