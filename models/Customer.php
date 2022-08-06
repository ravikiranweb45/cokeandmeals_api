<?php
namespace app\models;

use Yii;


class Customer extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  {
      return 'customers';
  }

    /** @inheritdoc */
    public function rules(){
        return[
          [['mobile_no'],'required'],
         // [['customer_name','mobile_no','city','address','access_token','email','default_lang'],'string'],
          [['status','state_id','pincode'],'integer'],
          [['created_date','dob','access_token_expiry','updated_date'],'safe'],

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


          public function getCustomerdetails($access_token,$pho_no)
          {
              $objCustomer=new Customer();
              $resCustomer=$objCustomer->find()->where(['access_token'=>$access_token])->andWhere(['mobile_no'=>$pho_no])->andWhere(['status'=>1])->one();
                      if($resCustomer || $resCustomer!=""){
                        $customerResult=['id'=>$resCustomer['id'],'access_token'=>$resCustomer['access_token'],'access_token_expiry'=>$resCustomer['access_token_expiry']];
                        return $customerResult;
                      }else{
                        throw new \yii\web\HttpException(404, 'The requested access_token could not be found.');
                      }  
         }

        public function editCustomerProfile($objCustomer,$pho_no,$address,$email,$customer_name,$dob,$city,$pincode,$gender,$state_id)
        {
                  $resCustomer=$objCustomer->find()->where(['mobile_no'=>$pho_no])->one();
                  $resCustomer->email=$email;
                  $resCustomer->customer_name=$customer_name;
                  $resCustomer->address=$address;
                  $resCustomer->mobile_no=$pho_no;
                  $resCustomer->dob=$dob;
                  $resCustomer->city=$city;
                  $resCustomer->pincode=$pincode;
                  $resCustomer->gender=$gender;
                  $resCustomer->state_id=$state_id;
                  $resCustomer->save();
                  return $resCustomer;

      }

}
