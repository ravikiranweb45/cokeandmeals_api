<?php
namespace app\models;

use Yii;


class Restaurant extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  {
      return 'restaurants';
  }

    /** @inheritdoc */
    public function rules(){
        return[
        //  [['restaurant_id','offer_id'],'required'],
      //    [['status','restaurant_id','offer_id','classification_id','is_spl_occasion','total_scan_count',],'integer'],
          [['created_date','updated_date','offers'],'safe'],
        [['restaurant_name','restaurant_code','latitude','longitude','restaurant_image','restaurant_tags'],'string'],
      ];
    }

    public function attributeLabels()
    {
        return [
          // 'id' => 'id',
          'restaurant_name' =>  'restaurant_name',
          'restaurant_code' => 'restaurant_code',
          'latitude' => 'latitude',
          'longitude' =>  'longitude',
          'restaurant_image' => 'restaurant_image',
          'restaurant_tags' => 'restaurant_tags',
          'address' =>  'address',
          'city' =>  'city',
          'country_id' => 'country_id',
          'pincode' => 'pincode',
          'mobile_no' =>  'mobile_no',
          'website_url' => 'website_url',
          'classification_ids' => 'classification_ids',
          'restaurant_content' =>  'restaurant_content',
          'description' =>  'description',
          'mon_open' => 'mon_open',
          'mon_close' => 'mon_close',
          'tue_open' =>  'tue_open',
          'tue_close' => 'tue_close',
          'wed_open' => 'wed_open',
          'wed_close' =>  'wed_close',
          'thu_open' =>  'thu_open',
          'thu_close' => 'thu_close',
          'fri_open' => 'fri_open',
          'fri_close' =>  'fri_close',
          'sat_open' => 'sat_open',
          'sat_close' => 'sat_close',
          'sun_open' =>  'sun_open',
           'thu_open' =>  'thu_open',
          'thu_close' => 'thu_close',
          'fri_open' => 'fri_open',
          'fri_close' =>  'fri_close',
          'sat_open' => 'sat_open',
          'sat_close' => 'sat_close',
          'sun_open' =>  'sun_open',
          'status' => 'status',
          'updated_date' => 'updated_date',
          'created_date' => 'created_date',
          
        ];
    }

    public function fields(){
        return [
            'offers' => function () {
                return $this->getRestaurantOffers();
            },
          ];
    }

    public function getRestaurantOffers(){
        return $this->hasOne(Restaurant_offer::className(), ['retaurant_id' => 'id']);
    }

}
