<?php
namespace app\models;

use Yii;


class Restaurant_offer extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  {
      return 'restaurant_offers';
  }

    /** @inheritdoc */
    public function rules(){
        return[
          [['restaurant_id','offer_id'],'required'],
          [['status','restaurant_id','offer_id','classification_id','is_spl_occasion','total_scan_count',],'integer'],
          [['created_date','start_date','end_date','updated_date'],'safe'],

      ];
    }

    public function attributeLabels()
    {
        return [
          // 'id' => 'id',
          'restaurant_id' =>  'restaurant_id',
          'offer_id' => 'offer_id',
          'start_date' => 'start_date',
          'end_date' =>  'end_date',
          'classification_id' => 'classification_id',
          'is_spl_occasion' => 'is_spl_occasion',
          'total_scan_count' =>  'total_scan_count',
          'status' => 'status',
          'updated_date' => 'updated_date',
          'created_date' => 'created_date',
          
        ];
    }


    public function getNearByResrestaurants($latitude,$longitude){
      $sql="SELECT * FROM ( SELECT id,restaurant_name,restaurant_code,city,latitude,longitude, ( 3959 * acos(cos(RADIANS($latitude)) * cos(radians(latitude::float)) * cos(radians(longitude::float) - radians($longitude)) + sin(RADIANS($latitude)) * sin(radians(latitude::float)))) AS distance FROM restaurants ORDER BY distance ) AS LocationDetails WHERE LocationDetails.distance <= (3*1000) ORDER BY distance LIMIT 10";
      return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function getResturantOffers($rest_Id){
      $sql="SELECT * FROM restaurant_offers AS ro
      JOIN offers AS offers ON (offers.id=ro.offer_id) where ro.restaurant_id =". $rest_Id ;
      // $resResturantOffer=Yii::$app->db->createCommand($sql)->queryAll(); 
         return Yii::$app->db->createCommand($sql)->queryAll();

    }

}
