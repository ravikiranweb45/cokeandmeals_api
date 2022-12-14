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
                'liststate'=>['get'],
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
        $behaviors['authenticator']['except'] = ['options', 'list','liststate', 'create-state', 'update-state', 'delete-state','import-state', 'get-state-asc',];

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


    public function actionLogin(){
        $json=file_get_contents('php://input');
        $jsonObj=json_decode($json);
        $pho_no=$jsonObj->pho_no;
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

    public function actionListstate()
    {   $id= $id=Yii::$app->request->get(name:'id');
        $objState=new State();
        if($id){
            $resState=$objState->find()->where(['id'=>$id])->andWhere(['status'=>1])->all();
           return $resState;
        }else{
            $resState=$objState->find()->andWhere(['status'=>1])->all();
           return $resState;
        }
    }


   
  
    public function actionImportState()
    {

        //installizing for the saving

        $success_saving_all = false;

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Data from the post excel.

        // Data from the post excel.

        $state = new State();

        // Add the data to the attributes

        $state->attributes = \yii::$app->request->post();

        $data = $state->excel_data;

        $connection = \Yii::$app->db;

        if (!empty($data)) {

            /*
             * Create Model of the state model
             * if it is present skip, else insert
             * create the db object of the user task model
             */

            $transaction = $connection->beginTransaction();
            /*
             * all state (if any) is valid. Save it all in one transactions.
             *
             */
            try {

                foreach ($data as $key => $value) {

                    $isExits = State::find()
                        ->where(['state_name' => $value['State Name']])
                        ->one();

                    if ($isExits === null) {

                        $stateModel = new State();

                        $stateModel->status = 1;

                        $stateModel->state_code = $value['State Code'];

                        $stateModel->state_name = $value['State Name'];

                        $region = Region::find()
                                    ->where(['region_name' => $value['Region Name']])
                                  ->one();

                        $stateModel->region_id = $region->id;

                        $stateModel->created_date = $date;

                        $stateModel->updated_date = $date;

                        $isValid = $stateModel->validate();

                        if ($isValid) {

                            $stateModel->save();

                            $success_saving_all = true;

                            // here, it means no exception was thrown during saving of user and its details (from the DB, for example).

                            // good - now commit it all...:
                        }
                    } else {
                        continue;
                    }

                }

                $transaction->commit();

            } catch (Exception $e) {

                // oops, saving user or its details failed. rollback, report us, and show the user an error.
                $transaction->rollback();

                $success_saving_all = false;
            }
            if ($success_saving_all) {
                //Set defalut response code 200 with the message;
                $response = \Yii::$app->getResponse();
                $response->setStatusCode(200);
                $responseData = [
                    'message' => 'State(s) imported successfully',
                ];

                return $responseData;
            } else {
                // Set the response and exit;
                $this->throwException(500, "Failed to import the states");
            }

        } else {

            $this->throwException(404, "Data Not Found");
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
     
    public function validateLatLong($latitude,$longitude){
            $lat_pattern  = '/\A[+-]?(?:90(?:\.0{1,18})?|\d(?(?<=9)|\d?)\.\d{1,18})\z/x';
            $long_pattern = '/\A[+-]?(?:180(?:\.0{1,18})?|(?:1[0-7]\d|\d{1,2})\.\d{1,18})\z/x';
            if(preg_match($lat_pattern, $lat)==1 && preg_match($long_pattern, $long)==1){
            return 1;
            }
    }

}
