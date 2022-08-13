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
          [['status','phone_number','customer_id'],'integer'],
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

    public function verifyOTP($customer_id,$otp){
      $sql = "SELECT clh.id,clh.customer_id,clh.phone_number,clh.otp, c.customer_name
              FROM customer_login_history as clh
              Join customers as c on (c.id = clh.customer_id)
              WHERE clh.status = 0 AND clh.customer_id = ".$customer_id." AND clh.otp = '".$otp."' ORDER BY id DESC LIMIT 1";
        $data= Yii::$app->db->createCommand($sql)->queryOne();
        if(isset($data) && !empty($data))
            return $data;
        else
            return 0;
    }

}
