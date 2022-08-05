<?php
namespace app\models;

use Yii;


class State extends \yii\db\ActiveRecord{



  /**
     * @inheritdoc
     */
  public static function tableName()
  
  {
    return 'states';
  }

    /** @inheritdoc */
    public function rules(){
        return[
          [['state_code','state_name'],'required'],
          [['state_code','state_name'],'string'],
          [['status'],'integer'],
          [['created_date',],'safe'],

      ];
    }

    public function attributeLabels()
    {
        return [
          // 'id' => 'id',
          'state_name' =>  'state_name',
          'state_code' => 'state_code',
          'status' => 'status',
          'created_date' => 'created_date',
          
        ];
    }

}
