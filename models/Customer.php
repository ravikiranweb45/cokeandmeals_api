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

}
