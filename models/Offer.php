<?php
namespace app\models;

use Yii;


class Offer extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  {
      return 'offers';
  }

    /** @inheritdoc */
    public function rules(){
        return[
        //  [['restaurant_id','offer_id'],'required'],
          [['offer_name','offer_code'],'string'],
          [['status','discount_value','is_combo_offer','is_spl_offer'],'integer'],
          [['created_date','start_date','end_date','updated_date'],'safe'],

      ];
    }

    public function attributeLabels()
    {
        return [
          'offer_name' =>  'offer_name',
          'offer_code' => 'offer_code',
          'discount_value' => 'discount_value',
          'brands_involved' =>  'brands_involved',
          'is_combo_offer' => 'is_combo_offer',
          'is_spl_offer' => 'is_spl_offer',
          'status' => 'status',
          'updated_date' => 'updated_date',
          'created_date' => 'created_date',
          
        ];
    }

}
