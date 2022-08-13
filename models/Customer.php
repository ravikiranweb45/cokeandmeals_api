<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Firebase\JWT\JWT;
use yii\rbac\Permission;
use yii\web\Request as WebRequest;

use Yii;


class Customer extends \yii\db\ActiveRecord
{

    const STATUS_DELETED = -10;
    const STATUS_DISABLED = 0;
    const STATUS_PENDING = -1;
    const STATUS_ACTIVE = 1;
    /** @var  string to store JSON web token */
    public $access_token;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customers';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['mobile_no'], 'required'],
            // [['customer_name','mobile_no','city','address','access_token','email','default_lang'],'string'],
            [['status', 'state_id', 'pincode'], 'integer'],
            [['created_date', 'dob', 'access_token_expiry', 'updated_date'], 'safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            // 'id' => 'id',
            'customer_name' =>  'customer_name',
            'mobile_no' => 'mobile_no',
            'city' => 'city',
            'dob' => 'dob',
            'address' => 'address',
            'state_id' => 'state_id',
            'pincode' => 'pincode',
            'gender' => 'gender',
            'pincode' => 'pincode',
            'status' => 'status',
            'created_date' => 'created_date',
            'updated_date' => 'updated_date',
            'access_token' => 'access_token',
            'access_token_expiry' => 'access_token_expiry',
            'email' => 'email',
            'default_lang' => 'default_lang',

        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getCustomerdetails($access_token, $customer_id)
    {
        $sql = "SELECT *
              FROM customers 
              WHERE status = 1
              AND access_token = '".$access_token."' AND id = $customer_id";
        $resCustomer= Yii::$app->db->createCommand($sql)->queryOne();
        if(!empty($resCustomer) || $resCustomer == ''){
            return $resCustomer;
        }
    }

    public function editCustomerProfile($objCustomer, $pho_no, $address, $email, $customer_name, $dob, $city, $pincode, $gender, $state_id)
    {
        $resCustomer = $objCustomer->find()->where(['mobile_no' => $pho_no])->one();
        $resCustomer->email = $email;
        $resCustomer->customer_name = $customer_name;
        $resCustomer->address = $address;
        $resCustomer->mobile_no = $pho_no;
        $resCustomer->dob = $dob;
        $resCustomer->city = $city;
        $resCustomer->pincode = $pincode;
        $resCustomer->gender = $gender;
        $resCustomer->state_id = $state_id;
        $resCustomer->save();
        return $resCustomer;
    }

    public function generateAccessToken()
    {
        // generate access token
        //        $this->access_token = Yii::$app->security->generateRandomString();
        $tokens = $this->getJWT();
       
        $this->access_token = $tokens[0];   // Token
        $this->access_token_expiry = date("Y-m-d H:i:s", $tokens[1]['exp']); // Expire

    }

    public function beforeSave($insert)
    {
        // Convert username to lower case
        $this->mobile_no = strtolower($this->mobile_no);

        // Fill unconfirmed email field with email if empty
        /*if($this->unconfirmed_email == '') {
            $this->unconfirmed_email = $this->email;
        }*/

        // Fill registration ip with current ip address if empty
        /*if($this->registration_ip == '') {
            $this->registration_ip = Yii::$app->request->userIP;
        }*/

        // Fill auth key if empty
        if($this->access_token == '') {
            $this->generateAuthKey();
        }

        return parent::beforeSave($insert);
    }

    public function generateAuthKey()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /*
	 * JWT Related Functions
	 */

    /**
     * Store JWT token header items.
     * @var array
     */
    protected static $decodedToken;

    protected static function getSecretKey()
    {
        return Yii::$app->params['jwtSecretCode'];
    }

    // And this one if you wish
    protected static function getHeaderToken()
    {
        return [];
    }


    /**
     * Logins user by given JWT encoded string. If string is correctly decoded
     * - array (token) must contain 'jti' param - the id of existing user
     * @param  string $accessToken access token to decode
     * @return mixed|null          User model or null if there's no user
     * @throws \yii\web\ForbiddenHttpException if anything went wrong
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $secret = static::getSecretKey();
        // Decode token and transform it into array.
        // Firebase\JWT\JWT throws exception if token can not be decoded
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            return false;
        }
        static::$decodedToken = (array) $decoded;
        // If there's no jti param - exception
        if (!isset(static::$decodedToken['jti'])) {
            return false;
        }
        // JTI is unique identifier of user.
        // For more details: https://tools.ietf.org/html/rfc7519#section-4.1.7
        $id = static::$decodedToken['jti'];
        return static::findByJTI($id);
    }

    /**
     * Finds User model using static method findOne
     * Override this method in model if you need to complicate id-management
     * @param  string $id if of user to search
     * @return mixed       User model
     */
    public static function findByJTI($id)
    {
        /** @var User $user */
        $user = static::find()->where([
            '=', 'id', $id
        ])
            ->andWhere([
                '=', 'status',  self::STATUS_ACTIVE
            ])
            ->andWhere([
                '>', 'access_token_expired_at', new Expression('NOW()')
            ])->one();
        if (
            $user !== null &&
            ($user->getIsBlocked() == true || $user->getIsConfirmed() == false)
        ) {
            return null;
        }
        return $user;
    }

    /**
     * Getter for encryption algorytm used in JWT generation and decoding
     * Override this method to set up other algorytm.
     * @return string needed algorytm
     */
    public static function getAlgo()
    {
        return 'HS256';
    }

    /**
     * Returns some 'id' to encode to token. By default is current model id.
     * If you override this method, be sure that findByJTI is updated too
     * @return integer any unique integer identifier of user
     */
    public function getJTI()
    {
        return $this->getId();
    }

    /**
     * Encodes model data to create custom JWT with model.id set in it
     * @return array encoded JWT
     */
    public function getJWT()
    {
        // Collect all the data
        $secret      = static::getSecretKey();
        $currentTime = time();
        $expire      = $currentTime + 86400; // 1 day
        $request     = Yii::$app->request;
        $hostInfo    = '';
        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }

        // Merge token with presets not to miss any params in custom
        // configuration
        $token = array_merge([
            'iat' => $currentTime,      // Issued at: timestamp of token issuing.
            'iss' => $hostInfo,         // Issuer: A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
            'aud' => $hostInfo,
            'nbf' => $currentTime,       // Not Before: Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid 10 seconds
            'exp' => $expire,           // Expire: Timestamp of when the token should cease to be valid. Should be greater than iat and nbf. In this case, the token will expire 60 seconds after being issued.
            'data' => [
                'mobile_no'      =>  $this->mobile_no,
                // 'roleLabel'    =>  $this->getRoleLabel(),
                'lastLoginAt'   =>  $this->updated_date,
            ]
        ], static::getHeaderToken());
        // Set up id
        $token['jti'] = $this->getJTI();    // JSON Token ID: A unique string, could be used to validate a token, but goes against not having a centralized issuer authority.
        return [JWT::encode($token, $secret, static::getAlgo()), $token];
    }
}
