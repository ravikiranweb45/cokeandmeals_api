<?php

namespace app\models;

use Yii;


class Restaurant_offer extends \yii\db\ActiveRecord
{



  /**
   * @inheritdoc
   */
  public static function tableName()
  {
    return 'restaurant_offers';
  }

  /** @inheritdoc */
  public function rules()
  {
    return [
      [['restaurant_id', 'offer_id'], 'required'],
      [['status', 'restaurant_id', 'offer_id', 'classification_id', 'is_spl_occasion', 'total_scan_count',], 'integer'],
      [['created_date', 'start_date', 'end_date', 'updated_date'], 'safe'],

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


  public function getNearByResrestaurants($latitude, $longitude)
  {
    $sql = "SELECT * FROM ( SELECT *, ( 3959 * acos(cos(RADIANS($latitude)) * cos(radians(latitude::float)) * cos(radians(longitude::float) - radians($longitude)) + sin(RADIANS($latitude)) * sin(radians(latitude::float)))) AS distance FROM restaurants ORDER BY distance ) AS LocationDetails WHERE LocationDetails.distance <= (3*1000) ORDER BY distance LIMIT 10";
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  public function getResturantOffers($rest_Id)
  {
    $sql = "SELECT ro.id AS retuarant_offer_id,ro.restaurant_id, ro.offer_id, ro.start_date, ro.end_date, ro.classification_id, ro.is_spl_occasion,
      ro.total_scan_count, ro.status AS restaurant_offers_status8, Offersss.id AS offer_id, Offersss.offer_name, Offersss.discount_value, Offersss.brands_involved,
      Offersss.is_combo_offer, Offersss.status as offers_status FROM restaurant_offers AS ro
      LEFT JOIN offers AS Offersss ON (Offersss.id=ro.offer_id) where ro.restaurant_id =" . $rest_Id;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  public function validateLatLong($latitude, $longitude)
  {
    // $lat_pattern  = '/\A[+-]?(?:90(?:\.0{1,18})?|\d(?(?<=9)|\d?)\.\d{1,18})\z/x';
    // $long_pattern = '/\A[+-]?(?:180(?:\.0{1,18})?|(?:1[0-7]\d|\d{1,2})\.\d{1,18})\z/x';
    $lat_pattern = '/^(90|[1-8][0-9]{1,20}|[0-9][.][0-9]{1,20})$/';
    $long_pattern = '/^(180|1[1-7][0-9]{1,20}|[1-9][0-9][.][0-9]{1,20}|[0-9][.][0-9]{1,20})$/';
    if (preg_match($lat_pattern, $latitude) == 1 && preg_match($long_pattern, $longitude) == 1) {
      return 1;
    }
  }

  public function getRestaurantDetails($restaurant_id)
  {
   /* resty.id AS restaurant_id,  resty.restaurant_name, resty.restaurant_code, resty.latitude, resty.longitude,
    resty.restaurant_image, resty.address, resty.city, resty.pincode, resty.mobile_no, resty.website_url, resty.restaurant_content, resty.description, resty.status AS restaurant_satus */
    $sql = "SELECT *  FROM restaurants where id =$restaurant_id";
    return Yii::$app->db->createCommand($sql)->queryOne();
  }

  public function getOfferDetails($restaurant_offer_id)
  {
    $sql = "SELECT resty.id, 
      ro.id AS retuarant_offer_id, ro.offer_id, ro.start_date, ro.end_date, ro.classification_id, ro.is_spl_occasion,
      ro.total_scan_count, ro.status AS restaurant_offers_status8, Offersss.id AS offer_id, Offersss.offer_name, Offersss.discount_value, Offersss.brands_involved,
      Offersss.is_combo_offer, Offersss.status as offers_status FROM restaurant_offers as ro
      LEFT JOIN  restaurants as resty ON (resty.id = ro.restaurant_id)
      LEFT JOIN offers AS Offersss ON (Offersss.id=ro.offer_id) where ro.id =" . $restaurant_offer_id;
    return Yii::$app->db->createCommand($sql)->queryOne();
  }

}
