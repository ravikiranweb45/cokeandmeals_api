<?php
namespace app\models;

use Yii;


class Classification extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  
  {
    return 'classifications';
  }

    /** @inheritdoc */
    public function rules(){
        return[
         
          [['class_name','class_desc'],'string'],
          [['status','class_order'],'integer'],
          [['created_date','updated_date'],'safe'],

      ];
    }

    public function attributeLabels()
    {
        return [
          'class_name' =>  'class_name',
          'class_desc' => 'class_desc',
          'class_order' =>  'class_order',
          'updated_date' => 'updated_date',
          'status' => 'status',
          'created_date' => 'created_date',
          
        ];
    }

}
