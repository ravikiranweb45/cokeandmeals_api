<?php
namespace app\models;

use Yii;


class Customer_wishlist extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  
  {
    return 'customer_wishlist';
  }

    /** @inheritdoc */
    public function rules(){
        return[
          [['status','restaurant_offer_id','customer_id'],'integer'],
          [['created_date'],'safe'],
      ];
    }

    public function attributeLabels()
    {
        return [
          'customer_id' =>  'customer_id',
          'restaurant_offer_id' => 'restaurant_offer_id',
          'status' => 'status',
          'created_date' => 'created_date',
        ];
    }

    public function check_whitelist($customer_id,$restaurant_offer_id){

          $sql="SELECT * FROM customer_wishlist WHERE restaurant_offer_id = ".$restaurant_offer_id." AND customer_id=".$customer_id;
          return Yii::$app->db->createCommand($sql)->queryOne();
        }

  
    public function get_customer_id($objCustomer,$pho_no){
      $resCustomer=$objCustomer->find()->where(['mobile_no'=>$pho_no])->andWhere(["status"=>1])->one();
      return $resCustomer['id'];
    }

}
