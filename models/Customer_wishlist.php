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

    public function customer_wishlist_resturant($resturant_id,$offer_id){

          $sql="SELECT ro.restaurant_id,r.restaurant_code,r.latitude,r.longitude,ro.offer_id,
          o.offer_name,o.offer_code,o.discount_value,o.brands_involved,ro.start_date,ro.end_date
          FROM restaurants AS r
          INNER JOIN restaurant_offers AS ro
          ON(ro.restaurant_id=r.id)
          INNER JOIN offers AS o
          ON(o.id=ro.offer_id) 
          WHERE ro.restaurant_id=".$resturant_id."AND ro.offer_id=".$offer_id;
          return Yii::$app->db->createCommand($sql)->queryAll();
    
        }

    public function customer_wishlist($objWishlist,$c_id,$resturant_id)
    {
          $objWishlist->customer_id=$c_id;
          $objWishlist->restaurant_offer_id=$resturant_id;
          $objWishlist->created_date=date('Y-m-d H:i:s');
          $objWishlist->save();
          return "Added To Your Cart";
    }
    public function get_customer_id($objCustomer,$pho_no){
      $resCustomer=$objCustomer->find()->where(['mobile_no'=>$pho_no])->andWhere(["status"=>1])->one();
      return $resCustomer['id'];
    }

}
