<?php

namespace app\modules\v1\controllers;

use app\filters\auth\HttpBearerAuth;
use app\models\Channels;
use app\models\City;
use app\models\Geographical;
use app\models\Geogroup;
use app\models\GeoGroupUser;
use app\models\Region;
use app\models\State;
use app\models\TSEDistributorMapping;
use app\models\User;
use app\models\UserDetails;
use app\models\UserPoint;
use app\models\UserRole;
use app\models\UserLogin;
use app\models\UserChangesLog;
use app\models\AbSfPrtlOutletmaster;
use app\models\AbSfUsermaster;
use app\models\GapAnalysisFile;
use app\models\UserPointExceptions;
use app\helpers\AppHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UsersController extends ActiveController
{
    public $modelClass = 'app\models\UserDetails';

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
                'index' => ['post'],
                'view' => ['get'],
                'create' => ['post'],
                'update' => ['put'],
                'delete' => ['delete'],
                'get-users' => ['get'],
                'create-user' => ['post'],
                'update-user' => ['put'],
                'search-users' => ['get'],
                'search-users-by-filter' => ['post'],
                'get-all-whitelisted-user' => ['post'],
                'import-users' => ['post'],
                'get-user-points' => ['get'],
                'create-user-point' => ['post'],
                'upload-user-point' => ['post'],
                'get-geo-group' => ['get'],
                'create-geo-group' => ['post'],
                'update-geo-group' => ['put'],
                'get-geo-group-users' => ['get'],
                'create-geo-group-users' => ['post'],
                'update-geo-group-users' => ['put'],
                'upload-geo-group-users' => ['post'],
                'update-user-mobile' => ['post'],
                'get-tse' => ['get'],
                'get-m2' => ['get'],
                'get-m3' => ['get'],
                'get-tmm' => ['get'],
                'get-supervisors' => ['get'],
                'outletgapanalysis' => ['post'],
                'getoutletgapanalysis' => ['get'],
                'gapanalysis' => ['post'],
                'gapanalysisreport' => ['post'],
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
                // Allow OPTIONS caching
                //'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination header to be exposed to the browser.
               //'Access-Control-Expose-Headers' => ['X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Page-Count', 'X-Pagination-Per-Page', 'X-Pagination-Total-Count'],
            ],
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = [
            'options', 'get-users', 'create-user', 'update-user', 'search-users', 'search-users-by-filter', 'get-all-whitelisted-user', 'import-users', 'get-user-points', 'create-user-point', 'upload-user-point', 'get-geo-group',
            'create-geo-group',
            'update-geo-group',
            'get-geo-group-users',
            'create-geo-group-users',
            'update-geo-group-users',
            'upload-geo-group-users',
            'update-user-mobile',
            'get-tse',
            'get-m2',
            'get-m3',
            'get-tmm',
            'get-supervisors',
            'outletgapanalysis',
            'getoutletgapanalysis',
            'gapanalysis',
            'gapanalysisreport'
        ];

        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index', 'view', 'create', 'update', 'delete', 'get-users', 'create-user', 'update-user', 'search-users', 'search-users-by-filter', 'get-all-whitelisted-user', 'import-users', 'create-user-point', 'upload-user-point', 'get-geo-group',
                        'create-geo-group',
                        'update-geo-group',
                        'get-geo-group-users',
                        'create-geo-group-users',
                        'update-geo-group-users',
                        'upload-geo-group-users',
                        'update-user-mobile',
                        'get-tse',
                        'get-m2',
                        'get-m3',
                        'get-tmm',
                        'get-supervisors',
                        'outletgapanalysis',
						'getoutletgapanalysis',
                        'gapanalysis',
                        'gapanalysisreport'
                    ],
                    'roles' => ['admin'],
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

    public function actionView($id)
    {
        /*$staff = User::find()->where([
        'id'    =>  $id
        ])->andWhere([
        '!=', 'status', -1
        ])->andWhere([
        'role'  =>  User::ROLE_USER
        ])->one();

        if($staff){
        return $staff;
        } else {
        throw new NotFoundHttpException("Object not found: $id");
        }*/
        return [];
    }

    public function actionCreate()
    {
        /*$model = new User();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(201);
        $id = implode(',', array_values($model->getPrimaryKey(true)));
        $response->getHeaders()->set('Location', Url::toRoute([$id], true));
        } else {
        // Validation error
        throw new HttpException(422, json_encode($model->errors));
        }

        return $model;*/
        return [];
    }

    public function actionUpdate($id)
    {
        /*$model = $this->actionView($id);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        } else {
        // Validation error
        throw new HttpException(422, json_encode($model->errors));
        }

        return $model;*/
        return [];
    }

    public function actionDelete($id)
    {
        /*$model = $this->actionView($id);

        $model->status = User::STATUS_DELETED;

        if ($model->save(false) === false) {
        throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(204);
        return "ok";*/
        return [];
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

    /**
     * function to get all the Users
     * @param $id
     * @param $null
     * @param $token
     * @author Suresh N
     */
    public function actionGetUsers()
    {

        $request = Yii::$app->request;

        $get = $request->get();
        // equivalent to: $get = $_GET;

        $id = $request->get('id');
        // equivalent to: $id = isset($_GET['id']) ? $_GET['id'] : null;

        // Get all Units data from the database.

        // returns all active units

        // SELECT * FROM `frequency` WHERE `status` = 0

        $users = new UserDetails();

        $provider = $users->search($get);

        // If the id is null;

        if ($id === null) {

            //Check if the units data store contains units (in case the database result returns NULL);

            if (!empty($users)) {
                return $provider;
            } else {
                // Set the response and exit;

                $this->throwException(204, "No User(s) were found");
            }
        }

        // Get the units from the array, using the id as key for retrieval.

        $user = null;

        if (!empty($users)) {
            $user = UserDetails::findOne($id);
        }

        // Send data based on the id

        if (!empty($user)) {

            return $user;
        } else {

            // Set the response and exit;

            $this->throwException(204, "No User is found");
        }
    }

    /**
     * function to get all the Users
     * @param $id
     * @param $null
     * @param $token
     * @author Suresh N
     */
    public function actionSearchUsers()
    {

        $request = \yii::$app->request->get();

        // Get all userdetails data from the database and search with the params.
        $searchModel = new UserDetails();

        $results = $searchModel->search($request);

        //Check if the users data store contains users (in case the database result returns NULL);

        if (!empty($results)) {

            return $results;
        } else {
            // Set the response and exit;

            $this->throwException(204, "No User(s) were found");
        }
    }

    /**
     * function to add Frequency
     * @param $model copy of the Unit
     * @param $null
     * @param $token
     * @author Suresh N
     */

    public function actionCreateUser()
    {

        //installizing for the saving

        $success_saving_all = false;

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Data from the post.

        $user = new User();

        $userDetail = new UserDetails();

        // Add the data to the attributes

        $user->attributes = \yii::$app->request->post();

        $userDetail->attributes = \yii::$app->request->post();

        $data = User::find()
            ->where(['username' => $user->username])
            ->one();

        if (empty($data)) {

            // Check if the email is same;

            $isEmail = UserDetails::find()
                ->where(['ro_email' => $userDetail->ro_email])
                ->one();

            // Add Status & Created Date to the user;

            $userDetail->created_date = $date;

            $userDetail->updated_date = $date;

            $userDetail->status = 1;

            $user->created_date = $date;

            $user->updated_date = $date;

            $user->password_hash = Yii::$app->security->generatePasswordHash($user->password_hash);

            $user->status = 1;

            if (empty($isEmail)) {
                // Insert to models;
                $isValid = $user->validate();

                $isValid = $userDetail->validate() && $isValid;

                if ($isValid) {

                    $connection = \Yii::$app->db;

                    /*
                     * all user (if any) is valid and user detail model is valid too. Save it all in one transaction. Save first the
                     * user as we need its 'id' attribute for its user records (user.user_id is NOT NULL in the DB level
                     * and a 'required' attribute in our model).
                     */
                    $trans = $connection->beginTransaction();

                    try {

                        // save the user;
                        $user->save();

                        $userDetail->user_id = $user->id;

                        $userDetail->save();

                        $success_saving_all = true;

                        // here, it means no exception was thrown during saving of user and its details (from the DB, for example).

                        // good - now commit it all...:

                        $trans->commit();
                    } catch (Exception $e) {
                        // oops, saving user or its details failed. rollback, report us, and show the user an error.
                        $trans->rollback();

                        $success_saving_all = false;
                    }
                }

                if ($success_saving_all) {
                    //Set defalut response code 200 with the message;
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'message' => 'User is created',
                    ];

                    return $responseData;
                } else {
                    // Set the response and exit;
                    $this->throwException(500, "Failed to create the Users");
                }
            } else {
                // Set the response and exit;
                $this->throwException(406, "Data Already Exists");
            }
        } else {

            // Set the response and exit;
            $this->throwException(406, "Data Already Exists");
        }
    }

    /**
     * update user.
     *
     * @param array|null put
     * @return string
     * @throws NotFoundHttpException
     */

    public function actionUpdateUser($id)
    {
        //installizing for the saving

        $success_saving_all = false;

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Find User Data Based on the Id;

        $user = User::findOne($id);

        $userDetail = UserDetails::find()
            ->where(['user_id' => $id])
            ->one();

        // Data from the put.

        // Load the data to the attributes

        $user->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $userDetail->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $data = User::find()
            ->where(['username' => $user->username])
            ->andWhere(['<>', 'id', $user->id])
            ->one();

        if (empty($data)) {

            // Check if the email is same;

            $isEmail = UserDetails::find()
                ->where(['ro_email' => $userDetail->ro_email])
                ->andWhere(['<>', 'user_id', $data->user_id])
                ->one();

            // Add Status & Created Date to the user;

            $userDetail->updated_date = $date;

            $user->updated_date = $date;

            $user->password_hash = Yii::$app->security->generatePasswordHash($user->password_hash);

            if (empty($isEmail)) {
                // Insert to models;
                $isValid = $user->validate();

                $isValid = $userDetail->validate() && $isValid;

                if ($isValid) {

                    $connection = \Yii::$app->db;

                    /*
                     * all user (if any) is valid and user detail model is valid too. Save it all in one transaction. Save first the
                     * user as we need its 'id' attribute for its user records (user.user_id is NOT NULL in the DB level
                     * and a 'required' attribute in our model).
                     */
                    $trans = $connection->beginTransaction();

                    try {

                        // save the user;
                        $user->save();

                        $userDetail->user_id = $user->id;

                        $userDetail->save();

                        $success_saving_all = true;

                        // here, it means no exception was thrown during saving of user and its details (from the DB, for example).

                        // good - now commit it all...:

                        $trans->commit();
                    } catch (Exception $e) {
                        // oops, saving user or its details failed. rollback, report us, and show the user an error.
                        $trans->rollback();

                        $success_saving_all = false;
                    }
                }

                if ($success_saving_all) {
                    //Set defalut response code 200 with the message;
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'message' => 'User is updated',
                    ];

                    return $responseData;
                } else {
                    // Set the response and exit;
                    $this->throwException(500, "Failed to update the User");
                }
            } else {
                // Set the response and exit;
                $this->throwException(406, "Data Email Already Exists");
            }
        } else {

            // Set the response and exit;
            $this->throwException(406, "Data Already Exists");
        }
    }

    /**
     * function to get all the Users filtered by the conditions
     * @param $id
     * @param $null
     * @param $token
     * @author Suresh N
     */
    public function actionSearchUsersByFilter()
    {

        $request = \yii::$app->request->post();

        // Get all userdetails data from the database and search with the params.
        $searchModel = new UserDetails();

        $results = $searchModel->searchByFilter($request);

        //Check if the users data store contains users (in case the database result returns NULL);

        if (!empty($results)) {

            return $results;
        } else {
            // Set the response and exit;

            $this->throwException(204, "No User(s) were found");
        }
    }

    /**
     * function to get all the Users
     * @param $id
     * @param $null
     * @param $token
     * @author Suresh N
     */
    public function actionGetAllWhitelistedUser()
    {

        $request = \yii::$app->request->post();

        // Get all points structure data from the database and search with the params.

        $searchModel = new UserDetails();

        $results = $searchModel->searchByUserModuleFilter($request);

        //Check if the points data store contains points (in case the database result returns NULL);

        if (!empty($results)) {

            return $results;
        } else {
            // Set the response and exit;

            $this->throwException(404, "No UserModule(s) were found");
        }
    }

    /**
     * function to add user(s) in excel
     * @param $model copy of the Unit
     * @param $null
     * @param $token
     * @author Suresh N
     */

    public function actionImportUsers()
    {

        //installizing for the saving

        $success_saving_all = false;

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Data from the post excel.

        $userDetail = new UserDetails();

        // Add the data to the attributes

        $userDetail->attributes = \yii::$app->request->post();

        $data = $userDetail->excel_data;

        $connection = \Yii::$app->db;

        if (!isset($userExcelData)) {

            /*
             * Create Model of the assignment model
             * if it is present skip, else insert
             * create the db object of the user task model
             */

            $transaction = $connection->beginTransaction();
            /*
             * all user (if any) is valid. Save it all in one transaction. Save first the
             * user as we need its 'id' attribute for its user records (user.user_id is NOT NULL in the DB level
             * and a 'required' attribute in our model).
             */
            try {

                foreach ($data as $key => $value) {

                    $isAllValid = false;

                    $isCodeValid = false;

                    $isEmailValid = false;

                    $user = User::find()
                        ->where(['username' => $value['User Phone Number']])
                        ->one();

                    #echo $user->createCommand()->getRawSql();

                    if ($user === null) {

                        // Check if the email and code is same;

                        $isEmailExits = UserDetails::find()
                            ->where(['ro_email' => $value['Outlet Email']])
                            ->one();

                        $isOutletCodeExits = UserDetails::find()
                            ->where(['ro_code' => $value['Outlet Code']])
                            ->one();

                        $isEmailValid = ($isEmailExits === null) ? true : false;

                        $isCodeValid = ($isOutletCodeExits === null) ? true : false;

                        $isAllValid = $isCodeValid && $isEmailValid;

                        if ($isAllValid) {

                            // Add Status & Created Date to the user;

                            $userModel = new User();

                            $userDetailModel = new UserDetails();

                            $userModel->attributes = $value;

                            $userDetailModel->attributes = $value;

                            $userDetailModel->created_date = $date;

                            $userDetailModel->updated_date = $date;

                            $userDetailModel->status = 1;

                            $userModel->created_date = $date;

                            $userModel->updated_date = $date;

                            $userModel->status = 1;

                            $userModel->username = $value['User Phone Number'];

                            if ($value['Reporting User Number'] !== null) {

                                $userData = User::find()
                                    ->where(['username' => $value['Reporting User Number']])
                                    ->one();

                                if ($userData === null) {
                                    continue;
                                }
                            }

                            $userModel->supervisor = $userData->id;

                            $regionData = Region::find()
                                ->where(['TRIM(region_name)' => $value['Region']])
                                ->one();

                            $GeoData = Geographical::find()
                                ->where(['TRIM(geograph_name)' => $value['Geography']])
                                ->one();

                            $channelData = Channels::find()
                                ->where(['TRIM(channel_desc)' => $value['Channel']])
                                ->one();

                            $stateData = State::find()
                                ->where(['TRIM(state_name)' => $value['State']])
                                ->one();
                            $cityData = City::find()
                                ->where(['TRIM(cityname)' => $value['City']])
                                ->one();

                            $userRoleData = UserRole::find()
                                ->where([
                                    'TRIM(role_name)' => $value['User Role'],
                                ])
                                ->one();
                            $userDetailModel->channel_type = $value['Channel Type'];

                            $userDetailModel->ro_code = $value['Outlet Code'];

                            $userDetailModel->ro_name = $value['Outlet Name'];

                            $userDetailModel->ro_email = $value['Outlet Email'];

                            $userDetailModel->user_detail_name = $value['Name'];

                            $userDetailModel->license_no = $value['Licence No'];

                            $userDetailModel->address = $value['Address'];

                            $userDetailModel->pincode = $value['Pincode'];

                            $userDetailModel->contact_no = $value['Alternate Contact Number'];

                            $userDetailModel->emp_code = $value['Employee Code'];

                            $userDetailModel->channel_id = $channelData->id;

                            $userDetailModel->region_id = $regionData->id;

                            $userDetailModel->geographical_id = $GeoData->id;

                            $userDetailModel->state_code = $stateData->state_code;

                            $userDetailModel->city = $cityData->cityname;

                            $userModel->user_role_id = $userRoleData->id;

                            // Insert to models;
                            $isValid = $userModel->validate();

                            $isValid = $userDetailModel->validate() && $isValid;

                            if ($isValid) {

                                // save the user;
                                $userModel->save();

                                $userDetailModel->user_id = $userModel->id;

                                if ($value['Distributors'] !== null) {

                                    $distributors = explode(',', $value['Distributors']);

                                    if (sizeof($distributors) > 0) {

                                        foreach ($distributors as $disKey => $disValue) {

                                            $distributorData = User::find()
                                                ->where(['username' => trim($disValue)])
                                                ->one();

                                            if ($distributorData !== null) {

                                                # add the distributor users to the mapping table;

                                                # create the TSEDistributorMapping;

                                                $tseDistributorMapping = new TSEDistributorMapping();

                                                $tseDistributorMapping->tse_id = $userData->id;

                                                $tseDistributorMapping->outlet_id = $userModel->id;

                                                $tseDistributorMapping->distributor_id = $distributorData->id;

                                                $tseDistributorMapping->createdon = $date;

                                                $tseDistributorMapping->update_date = $date;

                                                $tseValid = $tseDistributorMapping->validate();

                                                $duplicate = TSEDistributorMapping::find()
                                                    ->where(['tse_id' => $tseDistributorMapping->tse_id])
                                                    ->andWhere(['outlet_id' => $tseDistributorMapping->outlet_id])
                                                    ->andWhere(['distributor_id' => $tseDistributorMapping->outlet_id])
                                                    ->all();

                                                $notDuplicate = empty($duplicate) ? true : false;

                                                $tseValid = $notDuplicate && $tseValid;

                                                if ($tseValid) {

                                                    $tseDistributorMapping->save();
                                                }
                                            }
                                        }
                                    }
                                }

                                $userDetailModel->save();

                                $success_saving_all = true;

                                // here, it means no exception was thrown during saving of user and its details (from the DB, for example).

                                // good - now commit it all...:

                            }
                        }
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
                    'message' => 'User(s) imported successfully',
                ];

                return $responseData;
            } else {
                // Set the response and exit;
                $this->throwException(500, "Failed to import the Users");
            }
        } else {
            $this->throwException(404, "Data Not Found");
        }
    }

    /**
     * function to get all the Users
     * @param $id
     * @param $null
     * @param $token
     * @author Suresh N
     */
    public function actionGetUserPoints()
    {

        $request = Yii::$app->request;

        $get = $request->get();
        // equivalent to: $get = $_GET;

        $id = $request->get('id');
        // equivalent to: $id = isset($_GET['id']) ? $_GET['id'] : null;

        // Get all Units data from the database.

        // returns all active units

        // SELECT * FROM `frequency` WHERE `status` = 0

        $userPoints = new UserPoint();

        $provider = $userPoints->search($get);

        // If the id is null;

        if ($id === null) {

            //Check if the units data store contains units (in case the database result returns NULL);

            if (!empty($userPoints)) {

                return $provider;
            } else {
                // Set the response and exit;

                $this->throwException(204, "No User(s) Point(s) were found");
            }
        }

        // Get the units from the array, using the id as key for retrieval.

        $user = null;

        if (!empty($userPoints)) {
            $user = UserPoint::findOne($id);
        }

        // Send data based on the id

        if (!empty($user)) {

            return $user;
        } else {

            // Set the response and exit;

            $this->throwException(204, "No User Point(s) is found");
        }
    }

    /**
     * function to add user point
     * @param $model copy of the user point
     * @param $null
     * @param $token
     * @author Suresh N
     */

    public function actionCreateUserPoint()
    {
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        // Data from the post.
        $userPoint = new UserPoint();

        $userPointExceptions = new UserPointExceptions();

        $userPointExceptions->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $data = UserDetails::find()
            ->where(['(ro_code)' => (string) $userPointExceptions->user_id])
            ->one();

        if (!empty($data)) {
            $userPointExceptions->user_id = $data->user_id;

            if($userPointExceptions->points_type == 1){
                if($userPointExceptions->sku_based == 1){
                    $sql1 = "SELECT p.product_name, p.category_id, p.prod_id, b.brand_category_name
                                FROM products AS p 
                                JOIN brand_category AS b ON (b.id = p.category_id)
                                WHERE is_loyalty = 1 AND prod_id = $userPointExceptions->product_id";

                    $get_category_id = Yii::$app->db->createCommand($sql1)->queryOne();
                    $userPointExceptions->category_id = $get_category_id['category_id'];
                }

                if ($userPointExceptions->points_mehtod == 1) {
                
                    $userPoint->description = 'Points Earned in the month of '. date("F", mktime(0, 0, 0, $userPointExceptions->userpoints_month , 10)).' for achieving target on '. strtoupper($userPointExceptions->product_name)  .' category.';

                    //Update the points to the user;
                    $user = User::findOne($data->user_id);
                    $user->points += $userPointExceptions->points;
                    $userPoint->balance_points = $user->points;
                    $userPointExceptions->balance_points = $user->points;

                } else if ($userPointExceptions->points_mehtod == 2) {

                    $userPoint->description = 'Points debited in the month of '. date("F", mktime(0, 0, 0, $userPointExceptions->userpoints_month , 10)).' for '. strtoupper($userPointExceptions->product_name);
                    //Update the points to the user;
                    $user = User::findOne($userPointExceptions->user_id);
                    $user->points -= $userPointExceptions->points;
                    if ($user->points < 0) {
                        $this->throwException(422, "Cannot debit the points");
                    }
                    $userPoint->balance_points = $user->points;
                    $userPointExceptions->balance_points = $user->points;

                }
                $userPointExceptions->status = 1;

                if ($userPointExceptions->validate() && $userPointExceptions->save()) {

                    // Given a date in string format 
                    $dateFormation = $userPointExceptions->userpoints_year.'-'.$userPointExceptions->userpoints_month.'-01';
                    // Last date of given month.
                    $lastdate = date("Y-m-t", strtotime($dateFormation) );

                    //Can be used above or with the app helper can get start date and end date of month
                    //$apphelper         = new AppHelper();
                    //$dates_array       = $apphelper->getStartAndEndDatesFromMonthYr($userPointExceptions->userpoints_month,/$userPointExceptions->userpoints_year);

                    $userPoint->user_id = $data->user_id;
                    $userPoint->points = $userPointExceptions->points;
                    $userPoint->description = $userPoint->description;
                    $userPoint->created_date = $lastdate;
                    $userPoint->status = 1;
                    $userPoint->points_type = $userPointExceptions->points_type;
                  //  $userPoint->program_id = $userPointExceptions->program_id;
                    $userPoint->points_mehtod = $userPointExceptions->points_mehtod;
                    $userPoint->balance_points =  $userPoint->balance_points;
                    
                    $user->save();  
                    $userPoint->save();

                    $responseData = [
                        'message' => 'User Point is created',
                    ];

                    return $responseData;
                } else {
                    // Set the response and exit;
                    $this->throwException(500, json_encode($userPoint->errors));
                }

            } else {
                $dateFormation = $userPointExceptions->userpoints_year.'-'.$userPointExceptions->userpoints_month.'-01';
                // Last date of given month.
                $lastdate = date("Y-m-t", strtotime($dateFormation) );

                if ($userPoint->points_mehtod == 1) {
                    //Update the points to the user;
                    $user = User::findOne($data->user_id);
                    $user->points += $userPointExceptions->points;
                    $userPoint->balance_points = $user->points;

                } else if ($userPoint->points_mehtod == 2) {
                    //Update the points to the user;
                    $user = User::findOne($data->user_id);
                    $user->points -= $userPointExceptions->points;
                    if ($user->points < 0) {
                        $this->throwException(422, "Cannot debit the points");
                    }
                    $userPoint->balance_points = $user->points;

                }
                $userPoint->status = 1;
                $userPoint->user_id = $data->user_id;
                $userPoint->points = $userPointExceptions->points;
                $userPoint->description = $userPoint->description;
                $userPoint->created_date = $lastdate;
                $userPoint->points_type = $userPointExceptions->points_type;
                $userPoint->program_id = $userPointExceptions->program_id;
                $userPoint->points_mehtod = $userPointExceptions->points_mehtod;
                $userPoint->balance_points =  $userPoint->balance_points;

                if ($userPoint->validate() && $userPoint->save()) {

                    $user->save();

                    $responseData = [
                        'message' => 'User Point is created',
                    ];

                    return $responseData;
                } else {
                    // Set the response and exit;
                    $this->throwException(500, json_encode($userPoint->errors));
                }
            }
            // Add the data to the attributes
        } else {
            // Set the response and exit;
            $this->throwException(406, "Outlet code doesn't exit");
        }
    }

    public function actionUploadUserPoint()
    {
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        $connection = \Yii::$app->db;

        // Add the data to the attributes
        $request = \yii::$app->request->post();

        $userModel = new User();

        $users[] = $request['userpointsdata'];

        $insertedUsers = array();
        $notInsertedUsers = array();
        $outerArray = array();

        $transaction = $connection->beginTransaction();

        try {
            foreach ($users as $user) {
                for ($ucnt = 0; $ucnt < count($user); $ucnt++) {
                    $user_id = $userModel->getUserIdByOsmosys($user[$ucnt]["rocode"]);
                    $points = (int) $user[$ucnt]["points"];
                    $points_mehtod = ($user[$ucnt]["points_mehtod"] === "Credit") ? 1 : 2;
                    $points_type = (int) ($user[$ucnt]["points_type"]);
                    $description = $user[$ucnt]["description"];

                    if (isset($user_id) && !empty($user_id)) {
                        $userPoint = new UserPoint();
                        $userPoint->created_date = $date;
                        $userPoint->user_id = $user_id;
                        $userPoint->points = $points;

                        if ($points_mehtod == 1) {

                            $foundUser = User::findOne($user_id);
                            $foundUser->points += $points;
                            $userPoint->balance_points = $foundUser->points;
                        }
                        if ($points_mehtod == 2) {
                            $foundUser = User::findOne($user_id);
                            $foundUser->points -= $points;
                            if ($foundUser->points < 0) {

                                $outerArray = [
                                    'osmosys' => $user[$ucnt]["rocode"],
                                    'reason' => "Cannot " . $user[$ucnt]["points_mehtod"] . " the points",
                                ];

                                array_push($notInsertedUsers, $outerArray);

                                continue;
                            } else {
                                $userPoint->balance_points = $foundUser->points;
                            }
                        }

                        $userPoint->points_type = $points_type;
                        $userPoint->description = $description;
                        $userPoint->status = 1;
                        $userPoint->points_mehtod = $points_mehtod;

                        if ($userPoint->validate() && $userPoint->save()) {

                            $foundUser->save();

                            $outerArray = [
                                'osmosys' => $user[$ucnt]["rocode"],
                                'reason' => "User Point " . $user[$ucnt]["points_mehtod"] . " successfully",
                            ];

                            array_push($insertedUsers, $outerArray);
                        } else {
                            // Validation error
                            $this->throwException(422, $userPoint->error);
                        }
                    } else {
                        $outerArray = [
                            'osmosys' => $user[$ucnt]["rocode"],
                            'reason' => "Osmosys Code Not found",
                        ];
                        array_push($notInsertedUsers, $outerArray);
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            // oops, saving user or its details failed. rollback, report us, and show the user an error .
            $transaction->rollback();
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        $responseData = [
            'updatedusers' => $insertedUsers,
            'notupdatedusers' => $notInsertedUsers,
        ];

        return $responseData;
    }

    public function actionGetGeoGroup()
    {

        $request = Yii::$app->request;

        $get = $request->get();
        // equivalent to: $get = $_GET;

        $id = $request->get('id');
        // equivalent to: $id = isset($_GET['id']) ? $_GET['id'] : null;

        // Get all Units data from the database.

        // returns all active units

        $geoGroups = Geogroup::find()
            ->orderBy(['id' => SORT_DESC]);

        $provider = new ActiveDataProvider([
            'query' => $geoGroups,
            'pagination' => [
                'defaultPageSize' => 2,
                'pageSize' => 10,
                'pageSizeLimit' => [1, 2],
            ],
        ]);

        // If the id is null;

        if ($id === null) {

            //Check if the units data store contains units (in case the database result returns NULL);

            if (!empty($provider)) {

                return $provider;
            } else {
                // Set the response and exit;

                $this->throwException(204, "No Geo Group(s) Point(s) were found");
            }
        }

        // Get the units from the array, using the id as key for retrieval.

        $geoGroup = null;

        if (!empty($geoGroups)) {
            $geoGroup = Geogroup::findOne($id);
        }

        // Send data based on the id

        if (!empty($geoGroup)) {

            return $geoGroup;
        } else {

            // Set the response and exit;

            $this->throwException(204, "No Geo Group is found");
        }
    }

    /**
     * function to add target
     * @param $model copy of the target
     * @param $null
     * @param $token
     * @author Suresh N
     */

    public function actionCreateGeoGroup()
    {

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Data from the post.

        $geoGroup = new Geogroup();

        // Add the data to the attributes

        $geoGroup->attributes = \yii::$app->request->post();

        $data = Geogroup::find()
            ->where(['group_name' => $geoGroup->group_name])
            ->andWhere(['status' =>
            Geogroup::STATUS_ACTIVE])
            ->all();

        if (empty($data)) {

            $geoGroup->created_date = $date;

            $geoGroup->updated_date = $date;

            $geoGroup->status = 1;

            if ($geoGroup->validate() && $geoGroup->save()) {

                $response = \Yii::$app->getResponse();
                $response->setStatusCode(201);

                $responseData = [
                    'id' => $geoGroup->id,
                    'message' => 'Geo Group is created',
                ];

                return $responseData;
            } else {
                // Set the response and exit;
                $this->throwException(500, "Failed to create Target");
            }
        } else {

            // Set the response and exit;
            $this->throwException(406, "Data Already Exists");
        }
    }

    /**
     * update geography.
     *
     * @param array|null put
     * @return string
     * @throws NotFoundHttpException
     */

    public function actionUpdateGeoGroup($id)
    {

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        $geoGroup = Geogroup::findOne($id);

        // Check if the geography is exits;

        if (count($geoGroup) > 0) {

            $geoGroup->load(\Yii::$app->getRequest()->getBodyParams(), '');

            $isExits = Geogroup::find()
                ->where(['group_name' => $geoGroup->group_name])
                ->andWhere(['<>', 'id', $id])
                ->andWhere(['status' =>
                Geogroup::STATUS_ACTIVE])
                ->one();

            if (empty($isExits)) {
                // Load the geography data in to the $campaign;

                if ($geoGroup->validate() && $geoGroup->save()) {

                    //Set defalut response code 200 with the message;

                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'id' => $geoGroup->id,
                        'message' => 'Geo group is updated',
                    ];

                    return $responseData;
                } else {

                    // Set the response and exit;
                    $this->throwException(500, "Failed to update the Geo group");
                }
            } else {
                // Set the response and exit;
                $this->throwException(406, "Data Already Exists");
            }
        } else {

            // Set the response and exit;
            $this->throwException(404, "No Geo group Found");
        }
    }

    public function actionGetGeoGroupUsers()
    {

        $request = Yii::$app->request;

        $get = $request->get();
        // equivalent to: $get = $_GET;

        $id = $request->get('id');
        // equivalent to: $id = isset($_GET['id']) ? $_GET['id'] : null;

        // Get all Units data from the database.

        // returns all active units

        // SELECT * FROM `frequency` WHERE `status` = 0

        $users = new GeoGroupUser();

        $provider = $users->search($get);

        // If the id is null;

        if ($id === null) {

            //Check if the units data store contains units (in case the database result returns NULL);

            if (!empty($users)) {

                return $provider;
            } else {
                // Set the response and exit;

                $this->throwException(204, "No User(s) were found");
            }
        }

        // Get the units from the array, using the id as key for retrieval.

        $user = null;

        if (!empty($users)) {

            $user = GeoGroupUser::findOne($id);
        }

        // Send data based on the id

        if (!empty($user)) {

            return $user;
        } else {

            // Set the response and exit;

            $this->throwException(204, "No User is found");
        }
    }

    /**
     * function to add target
     * @param $model copy of the target
     * @param $null
     * @param $token
     * @author Suresh N
     */

    public function actionCreateGeoGroupUsers()
    {

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        // Data from the post.

        $geoGroupUser = new GeoGroupUser();

        // Add the data to the attributes

        $geoGroupUser->attributes = \yii::$app->request->post();

        $data = GeoGroupUser::find()
            ->where(['user_id' => $geoGroupUser->user_id])
            ->andWhere(['geo_group_id' => $geoGroupUser->geo_group_id])
            ->andWhere(['status' =>
            GeoGroupUser::STATUS_ACTIVE])
            ->all();

        if (empty($data)) {

            $geoGroupUser->created_date = $date;

            $geoGroupUser->updated_date = $date;

            $geoGroupUser->status = 1;

            if ($geoGroupUser->validate() && $geoGroupUser->save()) {

                $response = \Yii::$app->getResponse();
                $response->setStatusCode(201);

                $responseData = [
                    'id' => $geoGroupUser->id,
                    'message' => 'Geo Group User is created',
                ];

                return $responseData;
            } else {
                // Set the response and exit;
                $this->throwException(500, "Failed to Create Geo Group User");
            }
        } else {

            // Set the response and exit;
            $this->throwException(406, "Data Already Exists");
        }
    }

    /**
     * update geography.
     *
     * @param array|null put
     * @return string
     * @throws NotFoundHttpException
     */

    public function actionUpdateGeoGroupUsers($id)
    {

        // Current Date and Time;

        $date = date('Y-m-d H:i:s');

        $geoGroupUser = GeoGroupUser::findOne($id);

        // Check if the geography is exits;

        if (count($geoGroupUser) > 0) {

            $geoGroupUser->load(\Yii::$app->getRequest()->getBodyParams(), '');

            $isExits = GeoGroupUser::find()
                ->where(['user_id' => $geoGroupUser->user_id])
                ->andWhere(['geo_group_id' => $geoGroupUser->geo_group_id])
                ->andWhere(['<>', 'id', $id])
                ->andWhere(['status' =>
                GeoGroupUser::STATUS_ACTIVE])
                ->all();

            if (empty($isExits)) {
                // Load the geography data in to the $campaign;

                if ($geoGroupUser->validate() && $geoGroupUser->save()) {

                    //Set defalut response code 200 with the message;

                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'id' => $geoGroupUser->id,
                        'message' => 'Geo group User is updated',
                    ];

                    return $responseData;
                } else {

                    // Set the response and exit;
                    $this->throwException(500, "Failed to update the Geo group");
                }
            } else {
                // Set the response and exit;
                $this->throwException(406, "Data Already Exists");
            }
        } else {

            // Set the response and exit;
            $this->throwException(404, "No Geo group User Found");
        }
    }

    public function actionUploadGeoGroupUsers()
    {
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        $connection = \Yii::$app->db;

        // Add the data to the attributes
        $request = \yii::$app->request->post();

        $userModel = new User();

        $users[] = $request['users'];

        $geoGroupId = $request['geo_group_id'];

        $insertedUsers = array();
        $notInsertedUsers = array();
        $outerArray = array();
        $transaction = $connection->beginTransaction();

        try {
            foreach ($users as $user) {
                for ($ucnt = 0; $ucnt < count($user); $ucnt++) {
                    $user_id = $userModel->getUserIdByOsmosys($user[$ucnt]["rocode"]);
                    if (isset($user_id) && !empty($user_id)) {
                        $geoGroupUser = new GeoGroupUser();
                        $geoGroupUser->user_id = $user_id;
                        $geoGroupUser->geo_group_id = $geoGroupId;
                        $geoGroupUser->created_date = $date;
                        $geoGroupUser->updated_date = $date;
                        $geoGroupUser->status = 1;
                        $isExits = GeoGroupUser::find()
                            ->where(['user_id' => $geoGroupUser->user_id])
                            ->andWhere(['geo_group_id' => $geoGroupUser->geo_group_id])
                            ->andWhere(['status' =>
                            GeoGroupUser::STATUS_ACTIVE])
                            ->all();

                        if (empty($isExits)) {
                            if ($geoGroupUser->validate() && $geoGroupUser->save()) {
                                $outerArray = [
                                    'osmosys' => $user[$ucnt]["rocode"],
                                    'reason' => "Created successfully",
                                ];
                                array_push($insertedUsers, $outerArray);
                            } else {
                                // Validation error
                                $this->throwException(422, $geoGroupUser->error);
                            }
                        } else {
                            $outerArray = [
                                'osmosys' => $user[$ucnt]["rocode"],
                                'reason' => "Osmosys Code Already Exits",
                            ];
                            array_push($notInsertedUsers, $outerArray);
                        }
                    } else {
                        $outerArray = [
                            'osmosys' => $user[$ucnt]["rocode"],
                            'reason' => "Osmosys Code Not found",
                        ];
                        array_push($notInsertedUsers, $outerArray);
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            // oops, saving user or its details failed. rollback, report us, and show the user an error.
            $transaction->rollback();
        }

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        $responseData = [
            'updatedusers' => $insertedUsers,
            'notupdatedusers' => $notInsertedUsers,
        ];

        return $responseData;
    }

    public function actionUpdateUserMobile()
    {
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        $dateDMY = date("Y-m-d", strtotime($date));

        $connection = \Yii::$app->db;

        $insertedUsers = array();
        $notInsertedUsers = array();
        $outerArray = array();

        // Add the data to the attributes
        $request = \yii::$app->request->post();

        $userexcelupload = $request['userexcelupload'];

        $program_id = 1;
        $access_token = $this->getBearerAccessToken();

        if (isset($access_token)) {
            $userLogin      = new UserLogin();
            $userDetails    = $userLogin->getUserDetailsByAccessToken($program_id, $access_token);
            if (isset($userDetails['user_id']) && isset($userDetails['access_token_expired_at']) && $userDetails['access_token_expired_at'] > $date) {

                if ($userexcelupload == 0) {
                    $users[] = $request['userupdate'];

                    $user_id = $users[0][0]['user_id'];
                    $mobile = $users[0][0]['mobile'];

                    $founduser = User::findOne($user_id);

                    $founduserwithsamemobile = User::find()
                        ->where(['id' => $user_id])
                        ->andWhere(['username' => $mobile])
                        ->one();

                    $userDetail = UserDetails::find()
                        ->where(['user_id' => $user_id])
                        ->one();

                    if (isset($founduser) && !empty($founduser) && isset($userDetail) && !empty($userDetail)) {
                        $changed_log_memo = $founduser->changed_log_memo;

                        $data = User::find()
                            ->where(['username' => $mobile])
                            ->andWhere(['<>', 'id', $user_id])
                            ->one();
                        if (isset($data) && !empty($data)) {
                            $this->throwException(422, "Mobile Number already exist with rocode " . $data->dummy_rocode);
                        } else if (isset($founduserwithsamemobile) && !empty($founduserwithsamemobile)) {
                            $this->throwException(403, "No changes found for update");
                        } else {

                            $userChangesLog = new UserChangesLog();
                            $userChangesLog->user_id            =   $user_id;
                            $userChangesLog->exisiting_record   =   $founduser->username;
                            $userChangesLog->updated_record     =   $mobile;
                            $userChangesLog->record_type_id     =   1;
                            $userChangesLog->modified_date      =   $dateDMY;
                            $userChangesLog->created_by         =   $userDetails['username'];
                            $userChangesLog->updated_by         =   $userDetails['username'];
                            $userChangesLog->status             =   1;
                            $userChangesLog->created_date       =   $date;
                            $userChangesLog->updated_date       =   $date;

                            $userDetail->updated_date = $date;
                            $founduser->updated_date = $date;
                            $founduser->changed_log_memo = $changed_log_memo . " \n username updated from " . $founduser->username . " to " . $mobile . " on " . $dateDMY;
                            $founduser->username = $mobile;
                            $founduser->fcm_token = null;
                            $founduser->device_token = null;
                            $founduser->access_token_expired_at = null;

                            $userChangesLog->save();

                            $founduser->save();
                            $userDetail->save();

                            $response = \Yii::$app->getResponse();
                            $response->setStatusCode(200);
                            $responseData = [
                                'message' => 'User is updated',
                            ];

                            return $responseData;
                        }
                    } else {
                        $this->throwException(401, "User Not Found for Updation");
                    }
                } else if ($userexcelupload == 1) {
                    $users[] = $request['userupdate'];
                    $transaction = $connection->beginTransaction();
                    try {
                        foreach ($users as $user) {
                            for ($ucnt = 0; $ucnt < count($user); $ucnt++) {
                                $founduser = User::find()
                                    ->where(['dummy_rocode' => $user[$ucnt]["Outlet Code"]])
                                    ->one();

                                if (isset($founduser) && !empty($founduser)) {
                                    $founduserwithsamemobile = User::find()
                                        ->where(['id' => $founduser->id])
                                        ->andWhere(['username' => $user[$ucnt]["Phone"]])
                                        ->one();

                                    $userDetail = UserDetails::find()
                                        ->where(['user_id' => $founduser->id])
                                        ->one();

                                    $changed_log_memo = $founduser->changed_log_memo;
                                    $data = User::find()
                                        ->where(['username' => $user[$ucnt]["Phone"]])
                                        ->andWhere(['<>', 'id', $founduser->id])
                                        ->one();
                                    if (isset($data) && !empty($data)) {
                                        $outerArray = [
                                            'osmosys' => $user[$ucnt]["Outlet Code"],
                                            'reason' => "Mobile Number already exist with rocode " . $data->dummy_rocode,
                                        ];
                                        array_push($notInsertedUsers, $outerArray);
                                    } else if (isset($founduserwithsamemobile) && !empty($founduserwithsamemobile)) {
                                        $outerArray = [
                                            'osmosys' => $user[$ucnt]["Outlet Code"],
                                            'reason' => "No changes found for update",
                                        ];
                                        array_push($notInsertedUsers, $outerArray);
                                    } else {
                                        $userDetail->updated_date = $date;
                                        $founduser->updated_date = $date;

                                        $userChangesLog = new UserChangesLog();
                                        $userChangesLog->user_id            =   $founduser->id;
                                        $userChangesLog->exisiting_record   =   $founduser->username;
                                        $userChangesLog->updated_record     =   $user[$ucnt]["Phone"];
                                        $userChangesLog->record_type_id     =   1;
                                        $userChangesLog->modified_date      =   $dateDMY;
                                        $userChangesLog->created_by         =   $userDetails['username'];
                                        $userChangesLog->updated_by         =   $userDetails['username'];
                                        $userChangesLog->status             =   1;
                                        $userChangesLog->created_date       =   $date;
                                        $userChangesLog->updated_date       =   $date;

                                        $founduser->changed_log_memo = $changed_log_memo . " \n username updated from " . $founduser->username . " to " . $user[$ucnt]["Phone"] . " on " . $dateDMY;
                                        $founduser->username = $user[$ucnt]["Phone"];
                                        $founduser->fcm_token = null;
                                        $founduser->device_token = null;
                                        $founduser->access_token_expired_at = null;

                                        $userChangesLog->save();

                                        $founduser->save();
                                        $userDetail->save();

                                        $outerArray = [
                                            'osmosys' => $user[$ucnt]["Outlet Code"],
                                            'reason' => "User Updated successfully",
                                        ];
                                        array_push($insertedUsers, $outerArray);
                                    }
                                } else {
                                    $outerArray = [
                                        'osmosys' => $user[$ucnt]["Outlet Code"],
                                        'reason' => "Osmosys Code Not found",
                                    ];
                                    array_push($notInsertedUsers, $outerArray);
                                }
                            }
                        }
                        $transaction->commit();
                    } catch (Exception $e) {

                        // oops, saving user or its details failed. rollback, report us, and show the user an error.
                        $transaction->rollback();
                    }

                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'updatedusers' => $insertedUsers,
                        'notupdatedusers' => $notInsertedUsers,
                    ];

                    return $responseData;
                } else {
                    $this->throwException(400, "Invalid Request");
                }
            } else {
                throw new HttpException(401, json_encode("Unauthorized user access!!"));
            }
        } else {
            throw new HttpException(422, json_encode("Access Token is not set permission denied!"));
        }
    }

    public function actionGetSupervisors()
    {
        $usersModel = new User();

        $tsedata = $usersModel->getUsersByRoleId(4);
        $m2data = $usersModel->getUsersByRoleId(15);
        $m3data = $usersModel->getUsersByRoleId(14);
        $tmmdata = $usersModel->getUsersByRoleId(20);

        $response = \Yii::$app->getResponse();
        $response->setStatusCode(200);
        $responseData = [
            'tse' => $tsedata,
            'm2' => $m2data,
            'm3' => $m3data,
            'tmm' => $tmmdata,
        ];

        return $responseData;
    }

    public function actionOutletgapanalysis()
    {
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        $dateDMY = date("Y-m-d", strtotime($date));
        $starttime = microtime(true);
        $memoryusageStart = memory_get_usage();

        // Add the data to the attributes
        $request = \yii::$app->request->post();

        $gap_upload = $request['gap_upload'];

        $program_id = 1;
        $access_token = $this->getBearerAccessToken();
        
        $analysis_data = [];
        $newuser_template = [];
        $perfect_users = [];
        $newuser_ABData = [];

        $outerOsmosysLoad = [];
        $outerCsmMobileLoad = [];
        $rowdata=[];
        $AbSfUsermaster = new AbSfUsermaster();
        $AbSfPrtlOutletmaster = new AbSfPrtlOutletmaster();
        $folder = 'outlet_gap_analysis';
        $dirpath = 'uploads/' . $folder;

        if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777, true);
        }

        if (isset($access_token)) {

            $userLogin      = new UserLogin();
            $userDetails    = $userLogin->getUserDetailsByAccessToken($program_id, $access_token);

            if (isset($userDetails['user_id']) && isset($userDetails['access_token_expired_at']) && $userDetails['access_token_expired_at'] > $date) {
                
                if ($gap_upload == 1) //gap analysis
                {   

                    //Uploading Excle File
                    $appHelper = new AppHelper();
                    $fileuploads = $appHelper->uploadExcelFile($request['gap_file'], 'outlet_gap_analysis');

                    if(isset($fileuploads)){
                        $sql = "INSERT INTO gap_analysis_import( gap_file, created_date) VALUES ('".$fileuploads."',now());";
                        $query = Yii::$app->db->createCommand($sql)->execute();
                        $gap_analysis_import_id = Yii::$app->db->getLastInsertID();
                    }else{
                        throw new HttpException(422, json_encode('Uploading file failed, Please try again!'));
                    }



                    $gapdata = $request['gap_input_data'];
                    for ($i = 0; $i < sizeof($gapdata); $i++) {

                        $rocode         =   $gapdata[$i]["Osmosys Code"];
                        $csm_mobile     =   $gapdata[$i]["CSM Number"];
                        $m1_mobile      =   $gapdata[$i]["M1MobileNumber"];
                        $channel_desc   =   $gapdata[$i]["Classification"];
                        $csm_state      =   $gapdata[$i]["State"];
                        $csm_region     =   $gapdata[$i]["Region"];
                        $month          =   $gapdata[$i]["Month"];
                        $year           =   $gapdata[$i]["Year"];
                       

                        //GET CHANNEL DETAILS
                        if(isset($channel_desc) && !empty($channel_desc)){
                            $getChannel = Channels::find()
                                    ->where(['lower(channel_desc)' => strtolower($channel_desc)])
                                    ->one();
                                    $channels_id  = $getChannel['id'];
                        }else{
                            $channels_id  = 0;
                        }
                        

                        //GET STATE ID DETAILS
                        $getState   = State::find()
                                    ->where(['lower(state_name)' => strtolower($csm_state)])
                                    ->one();

                        $regionData = Region::find()
                                ->where(['TRIM(region_name)' => $csm_region])
                                ->one();
                                
                        $commonResponse = [
                            'rocode'        =>  $rocode,
                            'csm_mobile'    =>  $csm_mobile,
                            'm1_mobile'     =>  $m1_mobile,
                            'channel_desc'  =>  $channel_desc,
                            'channel_id'    =>  $channels_id,
                            'csm_state'     =>  $csm_state,
                            'state_code'    =>  $getState['state_code'],
                            'csm_region'    =>  $csm_region,
                            'region_id'     =>  $regionData['id'],
                            'month'         =>  $month,
                            'year'          =>  $year
                        ];
                        
                        
                        $b4t_status =   "";
                        $abi_status =   "";
                        $gap_remark =   "";
						$gap_status =   "Yes";

                        if(in_array($rocode,$outerOsmosysLoad ))
                        {
                            $gap_remark .= " Duplicate Osmosys Code, ";
                        }

                        if(in_array($csm_mobile,$outerCsmMobileLoad ))
                        {
                            $gap_remark .= " Duplicate CSM Mobile Number, ";
                        }


                        array_push($outerOsmosysLoad,$rocode );
                        array_push($outerCsmMobileLoad, $csm_mobile);

                        if(isset($rocode) && !empty($rocode))
                        {

                            $userByRocode = User::find()
                            ->where(['dummy_rocode' => $rocode])
                            ->one();

                            if (empty($userByRocode)) {
                                $searchAB = AbSfPrtlOutletmaster::find()
                                    ->where(['rocode' => $rocode])
                                    ->one();

                                if (empty($searchAB)) {
                                    $b4t_status =   "New User";
                                    $abi_status =   "Not Available";
                                } else {
                                    $b4t_status =   "New User";
                                    $abi_status =   "Available";
                                }
                            } else {
                                $b4t_status =   "Existing User";
                                $abi_status =   "Available";
                            }
                        }else{
                            $gap_remark .= " Invalid Osmosys Code, ";
                        }

                        if($b4t_status == 'New User' && $abi_status == 'Not Available')
                        {
                            $gap_remark .= "But Osmosys Code is not available in the ABI Staging Database ";
                        }

                        if (isset($csm_mobile) && !empty($csm_mobile) && strlen($csm_mobile) == 10) {

                            if($b4t_status == 'Existing User' && $abi_status == 'Available')
                            {

                                if($userByRocode->username != $csm_mobile)
                                {
                                    
                                    $gap_remark .= " CSM Mobile No has been changed, ";
									
                                  
                                    $userByMobile = User::find()
                                        ->where(['username' => $csm_mobile])
                                        ->one();  
                                       // print_r($userByMobile);exit;
                                    if($userByMobile->user_role_id == 5 && $userByMobile->dummy_rocode != null && $userByMobile->dummy_rocode != $rocode){
                                        $gap_remark .= " CSM Number already mapped to Osmosys Code " . $userByMobile->dummy_rocode . ", ";
                                    }else if($userByMobile->user_role_id != 5 && $userByMobile->username != null){
                                        $gap_remark .= " CSM Number already mapped to id " . $userByMobile->id . ", for role ".$userByMobile->user_role_id.", ";
                                    }else{
                                        $gap_remark .= "";
                                    }
									
                                }else{
                                    $gap_remark .= "";
                                }
                            }else if($b4t_status == 'New User' && $abi_status == 'Available')
                            {
                                $userByMobile = User::find()
                                    ->where(['username' => $csm_mobile])
                                    ->one();

                                if($userByMobile->user_role_id == 5 && $userByMobile->dummy_rocode != null && $userByMobile->dummy_rocode != $rocode){
                                    $gap_remark .= " CSM Number already mapped to Osmosys Code " . $userByMobile->dummy_rocode . ", ";
                                }else if($userByMobile->user_role_id != 5 && $userByMobile->username != null){
                                    $gap_remark .= " CSM Number already mapped to id " . $userByMobile->id . ", for role ".$userByMobile->user_role_id.", ";
                                }else{
                                    $gap_remark .= "";
                                }
                            }else{
                                $gap_remark .= "";
                            }
                        }else {
                            $gap_remark .= " Invalid CSM Mobile No, ";
                        }

                        
                            


                        if(isset($m1_mobile) && !empty($m1_mobile) && strlen($m1_mobile) == 10)
                        {
                            if($b4t_status == 'Existing User' && $abi_status == 'Available')
                            {

                                $getm1ofRocode = User::find()
                                    ->where(['id'=>$userByRocode->supervisor])
                                    ->one();

                                $getTse = User::find()
                                    ->where(['username' => $m1_mobile])
                                    ->one();
                                
                                if($getm1ofRocode->username == $m1_mobile)
                                {
                                    $gap_remark .= "";
                                }else{
                                    $gap_remark .= " M1 has been changed, ";
                                }
                                
                                if(empty($getTse))
                                {
                                    $searchM1inAB = $AbSfUsermaster->getABjoinBCDetails($rocode);

                                    if(isset($searchM1inAB  ) && !empty($searchM1inAB))
                                    {
                                        
                                    }else{
                                        $gap_remark .= " M1 is not found in ABINBEV Data, ";
                                    }
                                }else{
                                    
                                }
                            }elseif($b4t_status == 'New User' && $abi_status == 'Available')
                            {
                               $getTse = User::find()
                                    ->where(['username' => $m1_mobile])
                                    ->one();
                                
                                if(empty($getTse))
                                {
                                    $searchM1inAB = $AbSfUsermaster->getABjoinBCDetails($rocode);

                                    if(empty($searchM1inAB))
                                    {
                                        $gap_remark .= " M1 is not found in ABINBEV Data, ";
                                    }else{
                                        
                                    }
                                }else{
                                    
                                }
                            }else{

                            }
                        }else{
                            $gap_remark .= " Invalid M1 Mobile No, ";
                        }

                        if(isset($userByRocode) && !empty($userByRocode))
                        {
                            $getUserDetails = UserDetails::find()  
                                ->where(['user_id' => $userByRocode->id])
                                ->one();

                            if(isset($getUserDetails) && !empty($getUserDetails))
                            {
                                $getChannel = Channels::find()
                                    ->where(['lower(channel_desc)' => strtolower($channel_desc)])
                                    ->one();
                                
                                if(isset($getChannel) && $getChannel->id === $getUserDetails->channel_id)
                                {

                                }else{
                                    $gap_remark .= " Channel Doesnot match, ";
                                }
                            }
                        }else{
                            $searchAB = AbSfPrtlOutletmaster::find()
                                ->where(['rocode' => $rocode])
                                ->one();
                            
                            $getChannel = Channels::find()
                                ->where(['lower(channel_desc)' => strtolower($channel_desc)])
                                ->one();
                                
                            if(isset($searchAB) && !empty($searchAB))
                            {
                                if(isset($getChannel) && $getChannel->id === $searchAB->channel)
                                {

                                }else{
                                    $gap_remark .= " Channel Doesnot match at ABINBEV, ";
                                }
                            }
                        }

                        

                        if(isset($userByRocode) && !empty($userByRocode))
                        {
                            $getUserDetails = UserDetails::find()  
                                ->where(['user_id' => $userByRocode->id])
                                ->one();

                            if(isset($getUserDetails) && !empty($getUserDetails))
                            {
                                $getState = State::find()
                                    ->where(['lower(state_name)' => strtolower($csm_state)])
                                    ->andWhere(['state_year'=>2021])
                                    ->one();
                                
                                if(isset($getState) && $getState->state_code === $getUserDetails->state_code)
                                {

                                }else{
                                    $gap_remark .= " State Doesnot match";
                                }
                            }
                        }else{
                            $searchAB = AbSfPrtlOutletmaster::find()
                                ->where(['rocode' => $rocode])
                                ->one();
                            
                            $getState = State::find()
                                ->where(['lower(state_name)' => strtolower($csm_state)])
                                ->andWhere(['state_year'=>2021])
                                ->one();
                            
                            if(isset($searchAB) && !empty($searchAB))
                            {
                                if(isset($getState) && $getState->state_code === $searchAB->statecode)
                                {

                                }else{
                                    $gap_remark .= " State Doesnot match at ABINBEV";
                                }
                            }
                        }
						
						if(trim($gap_remark) == 'CSM Mobile No has been changed,'){
							$gap_status  = "No";
						}

                        if($gap_remark == "" || $gap_remark == null)
                        {
                            $gap_remark .= "Perfect";
							$gap_status  = "No";
                        }

                        $statusHeads = [
                            'b4t_status'    =>  $b4t_status,
                            'abi_status'    =>  $abi_status,
                            'gap_remark'    =>  $gap_remark,
							'gaps'			=>  $gap_status
                        ];

                        $rowdata = array_merge($commonResponse, $statusHeads);

                        array_push($analysis_data,$rowdata);

                        if($gap_remark == " Perfect ")
                        {
                            array_push($perfect_users, $rowdata);
                        }

                        if($b4t_status == 'New User' && $abi_status == 'Available')
                        {
                            //search for Osmosys Code in ABINBEV staging
                            $searchinAB = $AbSfUsermaster->getABjoinBCDetails($rocode);
                            array_push($newuser_template, $rowdata);
                            array_push($newuser_ABData, $searchinAB);
                        }

                        $insertData = "INSERT INTO gap_analysis_data(
                            gap_analysis_import_id, 
                            outlet_code, 
                            channel_id, 
                            csm_number, 
                            m1_mobile_number, 
                            state_code, 
                            region_id, 
                            bud4trade_status, 
                            abinbev_status, 
                            gap_analysis, 
                            gaps, 
                            gap_analysis_month,
                            gap_analysis_year)
                            VALUES (
                                $gap_analysis_import_id, 
                                '$rocode', 
                                $channels_id, 
                                '$csm_mobile', 
                                '$m1_mobile', 
                                '".$getState['state_code']."', 
                                '".$regionData['id']."', 
                                '".$statusHeads['b4t_status']."', 
                                '".$statusHeads['abi_status']."', 
                                '".$statusHeads['gap_remark']."', 
                                '".$statusHeads['gaps']."', 
                                '$month', 
                                '$year');";
                                
                        $queryData = Yii::$app->db->createCommand($insertData)->execute();

                    }

                    $stoptime  = microtime(true);
                    $loadtime = round(($stoptime - $starttime),2)." seconds taken for Server Execution";
                    
                    $memoryusageEnd = memory_get_usage();
                    $memoryusage = round(($memoryusageEnd - $memoryusageStart)/1024, 2)." KB of memory consumed for this analysis";
                    
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'executiontime'=> $loadtime,
                        'memoryusage'=>$memoryusage,
                        'analysis_data_model' => $analysis_data,
                        'analysis_data_model_new' => [],
                        'analysis_newusers_template_model' => $newuser_template,
                        'newuser_abdata' => $newuser_ABData,
                        'totalusersanalysed' => count($analysis_data)." User(s) has been analysed",
                        'totalnewusersfound' => count($newuser_template)." New User(s) found for this Gap Analysis",
                        'totalperfectstatus' => count($perfect_users)." User(s) Perfect Status found for this Gap Analysis ",
                        
                    ];

                    

                    return $responseData;
                } else if($gap_upload == 2) // new user import 
                {
                    $analysis_data = [];
                    $newuser_template = [];
                    $perfect_users = [];

                    $outerOsmosysLoad = [];
                    $outerCsmMobileLoad = [];
                    $rowdata=[];
                    
                    $connection = \Yii::$app->db;

                    $gapdata = $request['gap_input_data'];
                    for ($i = 0; $i < sizeof($gapdata); $i++) {
                        $rocode         =   $gapdata[$i]["Osmosys Code"];
                        $csm_mobile     =   $gapdata[$i]["Mobile"];
                        // $m1_mobile      =   $gapdata[$i]["M1MobileNumber"];
                        // $channel_desc   =   $gapdata[$i]["Classification"];
                        // $csm_state      =   $gapdata[$i]["State"];
                        // $csm_region     =   $gapdata[$i]["Region"];

                        $commonResponse = [
                            'rocode'        =>  $rocode,
                            'csm_mobile'    =>  $csm_mobile
                            // 'm1_mobile'     =>  $m1_mobile,
                            // 'channel_desc'  =>  $channel_desc,
                            // 'csm_state'     =>  $csm_state,
                            // 'csm_region'    =>  $csm_region
                        ];

                        $gap_remark =   "";
						$gap_status =   "Yes";
                        $succ = 0;

                        if(in_array($rocode,$outerOsmosysLoad ))
                        {
                            $gap_remark = " Duplicate Osmosys Code, ";
                        }

                        if(in_array($csm_mobile,$outerCsmMobileLoad ))
                        {
                            $gap_remark = " Duplicate CSM Mobile, ";
                        }

                        array_push($outerOsmosysLoad,$rocode );
                        array_push($outerCsmMobileLoad, $csm_mobile);

                        $userByRocode = User::find()
                            ->where(['dummy_rocode' => $rocode])
                            ->one();

                        $userByMobile = User::find()
                            ->where(['username' => $csm_mobile])
                            ->one();

                        if(isset($userByRocode) && !empty($userByRocode))
                        {
                            $gap_remark .= " Osmosys Code already exist ";
                        }else if(isset($userByMobile) && !empty($userByMobile)){
                            if($userByMobile->user_role_id != 5)
                            {
                                $gap_remark .= " Mobile No Already Mapped to ".$userByMobile->id." for user role ".$userByMobile->user_role_id." ";
                            }else{
                                $gap_remark .= " Mobile No Already Mapped to ".$userByMobile->dummy_rocode." ";
                            }                           
                        }else{
                            //search for Osmosys Code in ABINBEV staging
                            $searchinAB = $AbSfUsermaster->getABjoinBCDetails($rocode);
                            if(isset($searchinAB) && !empty($searchinAB))
                            {
                                $newuser = new User();
                                $newuser->username = $csm_mobile;
                                $newuser->supervisor = $searchinAB['tseid'];
                                $newuser->user_role_id = 5;
                                $newuser->program_id = 1;
                                $newuser->status = 1;
                                $newuser->existing_user_status = 1;
                                $newuser->dummy_rocode = $rocode;

                                $userDetail = new UserDetails();
                                $userDetail->ro_code = $rocode;
                                $userDetail->ro_name = $searchinAB['roname'];
                                $userDetail->channel_id = $searchinAB['channel'];
                                $userDetail->state_code = $searchinAB['statecode'];
                                $userDetail->ro_email = $searchinAB['owneremail'];
                                $userDetail->subbranch_id = $searchinAB['subbranchid'];
                                $userDetail->license_no = $searchinAB['license'];
                                $userDetail->lane = $searchinAB['lane'];
                                $userDetail->landmark = $searchinAB['landmark'];
                                $userDetail->area = $searchinAB['area'];
                                $userDetail->city = $searchinAB['city'];
                                $userDetail->pincode = (string)$searchinAB['pincode'];
                                $userDetail->contact_no = $searchinAB['contactno'];
                                $userDetail->emp_code = $searchinAB['employeecode'];
                                $userDetail->owner_name = $searchinAB['ownername'];
                                $userDetail->status = 1;

                                $isValid = $newuser->validate();

                                $isValid = $userDetail->validate() && $isValid;

                                if($isValid)
                                {
                                    //start transaction 
                                    $trans = $connection->beginTransaction();
                                    try{

                                        $newuser->save();

                                        $userDetail->user_id = $newuser->id;
                
                                        $userDetail->save();

                                        $trans->commit();

                                        $succ = 1;
                                        $gap_remark .= " User Created Successfully, Generated UserId is ".$newuser->id." ";
										$gap_status  = "No";

                                    }catch(Exception $e){
                                        $trans->rollback();
                                        $gap_remark .= " User creation failed Reason: ".$e->message()." ";
                                    }
                                }else{
                                    $gap_remark .= " User creation failed Reason: ".json_encode($userDetail->errors)." ";
                                }

                            }else{
                                $gap_remark .= " Osmosys not found in ABINBEV ";
                            }
                        }

                        $statusHeads = [
                            'gap_remark'    =>  $gap_remark,
							'gaps'			=>  $gap_status
                        ];

                        if($succ == 1){
                            array_push($perfect_users, $statusHeads);
                        }

                        $rowdata = array_merge($commonResponse, $statusHeads);

                        array_push($newuser_template, $rowdata);
                        array_push($analysis_data, $searchinAB);

                    }

                    $stoptime  = microtime(true);
                    $loadtime = round(($stoptime - $starttime),2)." seconds taken for Server Execution";

                    $memoryusageEnd = memory_get_usage();
                    $memoryusage = round(($memoryusageEnd - $memoryusageStart)/1024, 2)." KB of memory consumed for this analysis";

                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'executiontime'=> $loadtime,
                        'memoryusage'=>$memoryusage,
                        'analysis_data_model' => [],
                        'analysis_data_model_new' => $analysis_data,
                        'newuser_abdata' => [],
                        'analysis_newusers_template_model' => $newuser_template,
                        'totalusersanalysed' => count($newuser_template)." User(s) has been analysed",
                        'totalnewusersfound' => count($perfect_users)." User(s) has been saved successfully",
                        'totalperfectstatus' => count($newuser_template) - count($perfect_users) ." User(s) are failed to save, verify and upload again ",
                    ];

                    return $responseData;
                }else {
                    $this->throwException(400, "Invalid Request");
                }
            } else {
                throw new HttpException(401, json_encode("Unauthorized user access!!"));
            }
        } else {
            throw new HttpException(422, json_encode("Access Token is not set permission denied!"));
        }
    }
	
	public function actionGetoutletgapanalysis($month, $year,$state, $region ){
        try{              
            $gapanalysisreport = new User();                
            $resData= $gapanalysisreport->getGapanalysisreport($month, $year, $state, $region); 
            $response  = \Yii::$app->getResponse();
            $response->setStatusCode(200);  
            return $resData;  
            print_r($resData);exit;    
        }catch (\Exception $exception)   {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(404);  
            return new HttpException(404, json_encode("No Data Found!!"));
        }throw new HttpException(404, json_encode("No Data Found!!"));
    }

    public function actionGapanalysis(){
      
        // Current Date and Time;
        $date = date('Y-m-d H:i:s');
        $dateDMY = date("Y-m-d", strtotime($date));
        $starttime = microtime(true);
        $memoryusageStart = memory_get_usage();

        // Add the data to the attributes
        $request = \yii::$app->request->post();


        $gap_upload = $request['gap_upload'];

        $program_id = 1;
        $access_token = $this->getBearerAccessToken();
        
        $analysis_data = [];
        $newuser_template = [];
        $perfect_users = [];
        $newuser_ABData = [];

        $outerOsmosysLoad = [];
        $outerCsmMobileLoad = [];
        $rowdata=[];
        $duplicate_csm_in_sheet = [];
        $AbSfUsermaster = new AbSfUsermaster();
        $AbSfPrtlOutletmaster = new AbSfPrtlOutletmaster();
        $folder = 'outlet_gap_analysis';
        $dirpath = 'uploads/' . $folder;

        

        if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777, true);
        }

        if (isset($access_token)) {

            $userLogin      = new UserLogin();
            $userDetails    = $userLogin->getUserDetailsByAccessToken($program_id, $access_token);

            if (isset($userDetails['user_id']) && isset($userDetails['access_token_expired_at']) && $userDetails['access_token_expired_at'] > $date) {
                
                if ($gap_upload == 1) //gap analysis
                {   
                    //Uploading Excle File
                    $appHelper = new AppHelper();
                    $fileuploads = $appHelper->uploadExcelFile($request['gap_file'], 'outlet_gap_analysis');

                    if(isset($fileuploads)){
                        $sql = "INSERT INTO gap_analysis_import(gap_file, created_date) VALUES ('".$fileuploads."',now());";
                        $query = Yii::$app->db->createCommand($sql)->execute();
                        $gap_analysis_import_id = Yii::$app->db->getLastInsertID();

                    }else{
                        throw new HttpException(422, json_encode('Uploading file failed, Please try again!'));
                    }

                    $gapdata = $request['gap_input_data'];
                    foreach($gapdata as $infogap){
                        $osmosys_code1 =  $infogap['Osmosys Code'];
                        $csm_number1 = $infogap['CSM Number'];
                        //$state1 = $infogap['State'];
                        //$getState   = State::find()
                        //            ->where(['lower(state_name)' => strtolower($csm_number1)])
                        //            ->one();

                       // $sql = "INSERT INTO temp_gap_data (osmosys_code,csm_number,state,state_code) VALUES ('".$osmosys_code1."','".$csm_number1."','".$state1."','".$getState['state_code']."');";

                       $tgd = "INSERT INTO temp_gap_data (osmosys_code,csm_number) VALUES ('".$osmosys_code1."','".$csm_number1."');";
                       $query1 = Yii::$app->db->createCommand($tgd)->execute();

                    }

                    for ($i = 0; $i < sizeof($gapdata); $i++) {

                        $rocode         =   $gapdata[$i]["Osmosys Code"];
                        $csm_mobile     =   $gapdata[$i]["CSM Number"];
                        $csm_state      =   $gapdata[$i]["State"];

                        //GET STATE ID DETAILS
                        $getState   = State::find()
                                    ->where(['lower(state_name)' => strtolower($csm_state)])
                                    ->one();
                                
                        $commonResponse = [
                            'rocode'        =>  $rocode,
                            'csm_mobile'    =>  $csm_mobile,
                            'csm_state'     =>  $csm_state,
                            'state_code'    =>  $getState['state_code'],
                        ];

                        $outlet_big_gap = '';
                        $outlet_abi_gap = '';

                        $csm_big_gap = '';
                        $csm_abi_gap = '';

                        $state_big_gap = '';
                        $state_abi_gap = '';
                        
                        $b4t_status =   "";
                        $abi_status =   "";
                        $gap_remark =   "";
						$gap_status =   "GAP";

                        if(in_array($rocode,$outerOsmosysLoad ))
                        {
                            $gap_remark .= " Duplicate Osmosys Code, ";
                        }

                        array_push($outerOsmosysLoad,$rocode );

                        if(isset($rocode) && !empty($rocode))
                        {
                            $userByRocode = User::find()
                                            //->where(['dummy_rocode' => $rocode])
                                            ->where("dummy_rocode::INT = '".$rocode."'")
                                            ->one();
                            // $uByRocode = "SELECT * FROM users WHERE dummy_rocode::INTEGER = " . $rocode;
                            // $userByRocode = Yii::$app->db->createCommand($uByRocode)->queryOne();

                            if (empty($userByRocode)) {
                                $searchAB = AbSfPrtlOutletmaster::find()
                                    //->where(['rocode' => $rocode])
                                    ->where("rocode::INT = '".$rocode."'")
                                    ->one();

                                // $srAB = "SELECT * FROM ab_sf_prtl_outletmaster WHERE rocode::INTEGER = " . $rocode;
                                // $searchAB = Yii::$app->db->createCommand($srAB)->queryOne();

                                if (empty($searchAB)) {
                                    $b4t_status =   "New User";
                                    $abi_status =   "Not Available";

                                    $outlet_big_gap = 'GAP';
                                    $outlet_abi_gap = 'GAP';
                                    $gap_remark .= "New User,";
                                } else {
                                    $b4t_status =   "New User";
                                    $abi_status =   "Available";

                                    $outlet_big_gap = 'GAP';
                                    $outlet_abi_gap = 'OK';
                                    $gap_remark .= "New User, ";
                                }
                            } else {
                                $b4t_status =   "Existing User";
                                $abi_status =   "Available";

                                $outlet_big_gap = 'OK';
                                $outlet_abi_gap = 'OK';
                                $gap_remark .= "Exisitng User, ";
                            }
                        }else{
                            $gap_remark .= " Invalid Osmosys Code, ";
                        }

                        if($b4t_status == 'New User' && $abi_status == 'Not Available')
                        {
                            $gap_remark .= " But Osmosys Code is not available in ABI Staging DB,  ";
                        }

                        if (isset($csm_mobile) && !empty($csm_mobile) && strlen($csm_mobile) == 10) {

                            if($b4t_status == 'Existing User' && $abi_status == 'Available')
                            {

                                if($userByRocode->username != $csm_mobile)
                                {
                                    $csm_big_gap = 'GAP';
                                    $csm_abi_gap = 'OK';
                                    $userByMobile = User::find()
                                        ->where(['username' => $csm_mobile])
                                        ->one(); 
                                       // print_r($userByMobile);exit;

                                    if(!empty($userByMobile)){
                                        if($userByMobile->user_role_id == 5 && $userByMobile->dummy_rocode != null && $userByMobile->dummy_rocode != $rocode){
                                            $gap_remark .= " CSM Number already mapped to Osmosys Code " . $userByMobile->dummy_rocode . ", ";
                                        }else if($userByMobile->user_role_id != 5 && $userByMobile->username != null){
                                            $gap_remark .= " CSM Number already mapped to id " . $userByMobile->id . ", for role ".$userByMobile->user_role_id.", ";
                                        }else{
                                            $gap_remark .= "";
                                        }
                                    }else{
                                        $gap_remark .= "But CSM Mobile Number is changed, ";
									
                                        $csm_big_gap = 'GAP';
                                        $csm_abi_gap = 'OK';
                                    }
                                
                                }else{
                                    $gap_remark .= "";
                                    $csm_big_gap = 'OK';
                                    $csm_abi_gap = 'OK';
                                }
                            }else if($b4t_status == 'New User' && $abi_status == 'Available')
                            {
                                $userByMobile = User::find()
                                    ->where(['username' => $csm_mobile])
                                    ->one();
                                $csm_big_gap = 'GAP';
                                $csm_abi_gap = 'OK';
                                
                                if(!empty($userByMobile)){
                                    if($userByMobile->user_role_id == 5 && $userByMobile->dummy_rocode != null && $userByMobile->dummy_rocode != $rocode){
                                        $gap_remark .= " CSM Number already mapped to Osmosys Code " . $userByMobile->dummy_rocode . ", ";
                                    }else if($userByMobile->user_role_id != 5 && $userByMobile->username != null){
                                        $gap_remark .= " CSM Number already mapped to id " . $userByMobile->id . ", for role ".$userByMobile->user_role_id.", ";
                                    }else{
                                        $gap_remark .= "";
                                    }
                                }else{
                                    $gap_remark .= "New CSM Mobile Number, ";
                                
                                    $csm_big_gap = 'GAP';
                                    $csm_abi_gap = 'OK';
                                }
                            }else if($b4t_status == 'New User' && $abi_status == 'Not Available')
                            {
                                $userByMobile = User::find()
                                ->where(['username' => $csm_mobile])
                                ->one();

                                $csm_big_gap = 'GAP';
                                $csm_abi_gap = 'OK';
                            
                                if(!empty($userByMobile)){
                                    if($userByMobile->user_role_id == 5 && $userByMobile->dummy_rocode != null && $userByMobile->dummy_rocode != $rocode){
                                        $gap_remark .= " CSM Number already mapped to Osmosys Code " . $userByMobile->dummy_rocode . ", ";
                                    }else if($userByMobile->user_role_id != 5 && $userByMobile->username != null){
                                        $gap_remark .= " CSM Number already mapped to id " . $userByMobile->id . ", for role ".$userByMobile->user_role_id.", ";
                                    }else{
                                        $gap_remark .= "";
                                    }
                                }else{
                                    $gap_remark .= "New CSM Mobile Number, ";
                                
                                    $csm_big_gap = 'Gap';
                                    $csm_abi_gap = 'OK';
                                }
                            } else {
                                $gap_remark .= "";
                            }
                        }else {
                            $gap_remark .= " Invalid CSM Mobile No, ";
                        }

                        if(in_array($csm_mobile,$outerCsmMobileLoad ))
                        {
                            $gap_remark .= " Duplicate CSM Mobile Number, ";
                        }
                        array_push($outerCsmMobileLoad, $csm_mobile);

                        $search_csm = "Select * from temp_gap_data where csm_number = '". $csm_mobile ."'";
                        $QData = Yii::$app->db->createCommand($search_csm)->queryAll();
                        
                        //print_r($QData);exit;
                        foreach($QData as $info_csm){
                            array_push($duplicate_csm_in_sheet, $info_csm['osmosys_code']);
                        }
                        $gap_remark .="But CSM Mobile Number is also mapped to " .implode(',', $duplicate_csm_in_sheet);

                        if(trim($gap_remark) == 'But CSM Mobile Number is changed, '){
                            $gap_remark .= " But CSM Mobile Number is changed, ";
							$gap_status  = "No Gaps";
						}

                        if(isset($userByRocode) && !empty($userByRocode))
                        {
                            $getUserDetails = UserDetails::find()  
                                ->where(['user_id' => $userByRocode->id])
                                ->one();

                            if(isset($getUserDetails) && !empty($getUserDetails))
                            {
                                $getState = State::find()
                                    ->where(['lower(state_name)' => strtolower($csm_state)])
                                    ->andWhere(['state_year'=>2021])
                                    ->one();
                                
                                if(isset($getState) && $getState->state_code === $getUserDetails->state_code)
                                {
                                    $state_big_gap = 'OK';
                                    $state_abi_gap = 'OK';
                                }else{
                                    $state_big_gap = 'GAP';
                                    $state_abi_gap = 'OK';
                                    $gap_remark .= " State Does not match";
                                }
                            }
                        }else{
                            $searchAB = AbSfPrtlOutletmaster::find()
                                ->where(['rocode' => $rocode])
                                ->one();
                            
                            $getState = State::find()
                                ->where(['lower(state_name)' => strtolower($csm_state)])
                                ->andWhere(['state_year'=>2021])
                                ->one();
                            
                            if(isset($searchAB) && !empty($searchAB))
                            {
                                if(isset($getState) && $getState->state_code === $searchAB->statecode)
                                {
                                    $state_big_gap = 'OK';
                                    $state_abi_gap = 'OK';
                                }else{
                                    $state_big_gap = 'OK';
                                    $state_abi_gap = 'Gap';
                                    $gap_remark .= " State Doesnot match at ABINBEV";
                                }
                            }
                            
                        }

                        if($gap_remark == "Exisitng User, " || $gap_remark == 'New User, New CSM Mobile Number, ')
                        {
							$gap_status  = "No Gaps";
                        }

                        $statusHeads = [
                            'b4t_status'     =>   $b4t_status,
                            'abi_status'     =>   $abi_status,
                            'gap_remark'     =>   $gap_remark,
                            'outlet_big_gap' =>   $outlet_big_gap,
                            'outlet_abi_gap' =>   $outlet_abi_gap,
                            'csm_big_gap'    =>   $csm_big_gap,
                            'csm_abi_gap'    =>   $csm_abi_gap,
                            'state_big_gap'  =>   $state_big_gap,
                            'state_abi_gap'  =>   $state_abi_gap,
							'status'	     =>   $gap_status
                        ];

                        $rowdata = array_merge($commonResponse, $statusHeads);

                        array_push($analysis_data,$rowdata);

                        if($gap_remark == "No Gaps")
                        {
                            array_push($perfect_users, $rowdata);
                        }

                        if($b4t_status == 'New User' && $abi_status == 'Available')
                        {
                            //search for Osmosys Code in ABINBEV staging
                            $searchinAB = $AbSfUsermaster->getABjoinBCDetails($rocode);
                            array_push($newuser_template, $rowdata);
                            array_push($newuser_ABData, $searchinAB);
                        }
                        $out_let_gap_status = '';
                        $csm_gap_status = '';
                        $state_gap_status = '';

                        if($statusHeads['outlet_big_gap'] == 'OK' && $statusHeads['outlet_big_gap'] == 'OK'){
                            $out_let_gap_status = 'OK';
                        }else{
                            $out_let_gap_status = 'GAP';
                        }

                        if($statusHeads['csm_big_gap'] == 'OK' && $statusHeads['csm_abi_gap'] == 'OK'){
                            $csm_gap_status = 'OK';
                        }else{
                            $csm_gap_status = 'GAP';
                        }

                        if($statusHeads['state_big_gap'] == 'OK' && $statusHeads['state_abi_gap'] == 'OK'){
                            $state_gap_status = 'OK';
                        }else{
                            $state_gap_status = 'GAP';
                        }

                        $insertData = "INSERT INTO gap_analysed_data(
                            gap_analysis_import_id, 
                            outlet_code, 
                            csm_number, 
                            state_code, 
                            bud4trade_status, 
                            abinbev_status, 
                            gap_analysis_remarks, 
                            outlet_gap, 
                            csm_gap,
                            state_gap,
                            status,
                            created_date
                            )
                            VALUES (
                                $gap_analysis_import_id, 
                                '$rocode', 
                                '$csm_mobile', 
                                '".$getState['state_code']."', 
                                '".$statusHeads['b4t_status']."', 
                                '".$statusHeads['abi_status']."', 
                                '".$statusHeads['gap_remark']."', 
                                '".$out_let_gap_status."', 
                                '".$csm_gap_status."', 
                                '".$state_gap_status."', 
                                '".$statusHeads['status']."', 
                                now() 
                            );";
                                
                        $queryData = Yii::$app->db->createCommand($insertData)->execute();

                    }

                    $truncate_temporary_gap_ddat = Yii::$app->db->createCommand()->truncateTable('temp_gap_data')->execute();

                    $stoptime  = microtime(true);
                    $loadtime = round(($stoptime - $starttime),2)." seconds taken for Server Execution";
                    
                    $memoryusageEnd = memory_get_usage();
                    $memoryusage = round(($memoryusageEnd - $memoryusageStart)/1024, 2)." KB of memory consumed for this analysis";
                    
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(200);
                    $responseData = [
                        'executiontime'=> $loadtime,
                        'memoryusage'=>$memoryusage,
                        'analysis_data_model' => $analysis_data,
                        'analysis_data_model_new' => [],
                        'analysis_newusers_template_model' => $newuser_template,
                        'newuser_abdata' => $newuser_ABData,
                        'totalusersanalysed' => count($analysis_data)." User(s) has been analysed",
                        'totalnewusersfound' => count($newuser_template)." New User(s) found for this Gap Analysis",
                        'totalperfectstatus' => count($perfect_users)." User(s) Perfect Status found for this Gap Analysis ",
                    ];
                    return $responseData;
                }else {
                    $this->throwException(400, "Invalid Request");
                }
            } else {
                throw new HttpException(401, json_encode("Unauthorized user access!!"));
            }
        } else {
            throw new HttpException(422, json_encode("Access Token is not set permission denied!"));
        }
    }

    public function actionGapanalysisreport(){

        $request = \yii::$app->request->post();

        $start_date = $request['start_date'];
        $end_date  = $request['end_date'];

        $report = new GapAnalysisFile();

        $gap_report = $report::find()
        ->where(['and', "created_date::DATE>='$start_date'", "created_date::DATE<='$end_date'"])
        ->all();

        $gap_file = array();
        $gap_analysed_data = array();

        foreach($gap_report as $info){
        $grecords =array();
            
            $get_gap_records = "Select * from gap_analysed_data where gap_analysis_import_id::INT = " . $info['id'];
            $queryData = Yii::$app->db->createCommand($get_gap_records)->queryAll();
            if(!empty($queryData)){
                foreach($queryData as $infodata){
                    $analyzed_records = array(
                        'outlet_code' => $infodata['outlet_code'],
                        'csm_number' => $infodata['csm_number'],
                        'state_code' => $infodata['state_code'],
                        'bud4trade_status' => $infodata['bud4trade_status'],
                        'abinbev_status' => $infodata['abinbev_status'],
                        'gap_analysis_remarks' => $infodata['gap_analysis_remarks'],
                        'outlet_gap' => $infodata['outlet_gap'],
                        'csm_gap' => $infodata['csm_gap'],
                        'state_gap' => $infodata['state_gap'],
                        'status' => $infodata['status'],
                        'analyzed_date' => $infodata['created_date'],
                    );
                    array_push($grecords,$analyzed_records,);
                }
                
                $uploaded_file = array(
                    'file_name' => $info['gap_file'],
                    'gap_id' => $info['id'],
                    'created_date' => date('Y-m-d',strtotime($info['created_date'])),
                    'gap_records' => $grecords,
                );
    
                array_push($gap_file,$uploaded_file); 
                unset($grecords);
            }
           

        }

        $upload_file =  [
            'gap_file' => $gap_file
        ];

        return $upload_file;
    }
}
