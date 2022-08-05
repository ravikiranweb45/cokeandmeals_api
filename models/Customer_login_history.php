<?php
namespace app\models;

use Yii;


class Customer_login_history extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  {
      return 'customer_login_history';
  }

    /** @inheritdoc */
    public function rules(){
        return[
          [['phone_number'],'required'],
       //   [['customer_id','ip_address','access_token'],'string'],
          [['status','phone_number'],'integer'],
          [['loginat',],'safe'],

      ];
    }

    public function attributeLabels()
    {
        return [
          // 'id' => 'id',
          'customer_id' => 'customer_id',
          'loginat' => 'loginat',
          'otp' => 'otp',
          'status' => 'status',
          'phone_number' =>  'phone_number',
          'access_token' => 'access_token',
          'ip_address' => 'ip_address',
          
        ];
    }

}
