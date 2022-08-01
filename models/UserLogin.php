<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "userlogin_history".
 *
 * @property int $id
 * @property int $user_id
 * @property string $loginat
 * @property string $otp
 * @property int $status
 * @property int $campaign_id
 * @property string $phone_number
 * @property string $ipaddress
 * @property boolean $is_pin
 *  @property string $device_token
 */
class UserLogin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userlogin_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'phone_number'], 'required'],
            [['user_id', 'status'], 'default', 'value' => null],
            [['user_id', 'status'], 'integer'],
            [['loginat','ipaddress','otp'], 'safe'],
            [['phone_number'], 'string'],
            [['is_pin'],'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'loginat' => 'Loginat',
            'otp' => 'Otp',
            'status' => 'Status',
            'phone_number' => 'Phone Number',
            'is_pin'       => 'Is Pin'
        ];
    }

    /**
     * Check Existing User and Get OTP
     */

    public function checkExistingUser($program_id,$mobile_no){
        $sql = "SELECT Userr.id AS user_id,
                       UserLogin.otp,
                       Userr.user_role_id,
                       Userr.is_block,
                       Userr.is_static_otp
                FROM users AS Userr
                LEFT JOIN userlogin_history AS UserLogin ON (UserLogin.user_id = Userr.id)
                WHERE Userr.username = '".$mobile_no."'
                      AND Userr.program_id = ".$program_id."
                      AND Userr.status = 1
                ORDER BY UserLogin.id DESC 
                LIMIT 1";
        $data= Yii::$app->db->createCommand($sql)->queryAll();
        if(isset($data[0]) && !empty($data[0]))
            return $data[0];
        else
            return 0;
    }

    /**
     * Verify OTP
     */

    public function verifyOTP($user_id,$otp){
        $sql = "SELECT UserLogin.id,
                       UserLogin.user_id,
                       UserLogin.phone_number,
                       UserLogin.otp
                FROM
                (
                SELECT id,
                       user_id,
                       phone_number,
                       otp 
                FROM userlogin_history 
                WHERE status = 0
                      AND user_id = ".$user_id." 	
                ORDER BY id DESC 
                LIMIT 1
                ) AS UserLogin
                WHERE UserLogin.user_id = ".$user_id."
                      AND UserLogin.otp = '".$otp."'";
        $data= Yii::$app->db->createCommand($sql)->queryAll();
        if(isset($data[0]) && !empty($data[0]))
            return $data[0];
        else
            return 0;
    }

    /**
     * Verify access token and get Userdetails
     */

    public function getUserDetailsByAccessToken($program_id,$access_token){
        $sql = "SELECT Userr.id AS user_id, 
                       Userr.user_role_id, 
                       Userr.username, 
                       Userr.password_hash, 
                       Userr.supervisor, 
                       Userr.auth_key, 
                       Userr.access_token_expired_at, 
                       Userr.password_reset_token, 
                       Userr.default_lang, 
                       Userr.is_verified, 
                       Userr.last_login_at, 
                       Userr.last_login_ip, 
                       Userr.status, 
                       Userr.created_date, 
                       Userr.updated_date, 
                       Userr.points, 
                       Userr.wallet, 
                       Userr.generated_super, 
                       Userr.confirmed_at, 
                       Userr.blocked_at, 
                       Userr.device_token, 
                       Userr.source_from,
                       Userr.program_id, 
                       Userr.is_common_terms,
                       Userr.fcm_token,
                       Userr.app_version,
                       UserDetail.id AS user_detail_id,
                       UserDetail.geographical_id, 
                       UserDetail.channel_type, 
                       UserDetail.channel_id, 
                       UserDetail.sub_channel, 
                       UserDetail.state_code, 
                       UserDetail.ro_code, 
                       UserDetail.ro_name, 
                       UserDetail.ro_email, 
                       UserDetail.user_detail_name, 
                       UserDetail.subbranch_id, 
                       UserDetail.salestax_regno, 
                       UserDetail.class_code, 
                       UserDetail.group_id, 
                       UserDetail.license_no, 
                       UserDetail.outlet_img1, 
                       UserDetail.outlet_img2, 
                       UserDetail.profile_pic, 
                       UserDetail.address, 
                       UserDetail.lane, 
                       UserDetail.landmark, 
                       UserDetail.area, 
                       UserDetail.city, 
                       UserDetail.pincode, 
                       UserDetail.contact_no, 
                       UserDetail.business_from_time, 
                       UserDetail.business_to_time, 
                       UserDetail.cover_def, 
                       UserDetail.chiller_placed, 
                       UserDetail.signage_placed, 
                       UserDetail.stock_space, 
                       UserDetail.waiter_no, 
                       UserDetail.owner_name, 
                       UserDetail.owner_contact_no, 
                       UserDetail.owner_email, 
                       UserDetail.manager_name, 
                       UserDetail.manager_contact_no, 
                       UserDetail.manager_email, 
                       UserDetail.location_id, 
                       UserDetail.status, 
                       UserDetail.created_date AS userdetail_created_date, 
                       UserDetail.updated_date AS userdetail_updated_date,
                       UserDetail.document_no, 
                       UserDetail.document_file, 
                       UserDetail.gender, 
                       UserDetail.date_of_birth,
                       UserDetail.latitude, 
                       UserDetail.longitude, 
                       UserDetail.anniversary_date,
                       CASE WHEN Userr.default_lang = 'ka' THEN UserRole.role_name_ka 
                            WHEN Userr.default_lang = 'hi' THEN UserRole.role_name_hi
			                ELSE UserRole.role_name END AS role_name,
                       UserRole.role_icon,
                       CASE WHEN Userr.default_lang = 'ka' THEN UserRole.role_full_name_ka 
                            WHEN Userr.default_lang = 'hi' THEN UserRole.role_full_name_hi
			                ELSE UserRole.role_full_name END AS role_full_name,
                       CASE WHEN Userr.default_lang = 'ka' THEN Channel.channel_desc_ka 
                            WHEN Userr.default_lang = 'hi' THEN Channel.channel_desc_hi
			                ELSE Channel.channel_desc END AS channel_desc,
                       State.region_id
                FROM \"users\" AS Userr
                JOIN userdetails AS UserDetail ON (UserDetail.user_id = Userr.id)
                JOIN user_roles AS UserRole ON (UserRole.id = Userr.user_role_id)
                LEFT JOIN channels AS Channel ON (Channel.id = UserDetail.channel_id)
                LEFT JOIN states AS State ON (State.state_code = UserDetail.state_code)
                WHERE Userr.device_token = '".$access_token."' 
                      AND Userr.program_id = ".$program_id." ";
        $data= Yii::$app->db->createCommand($sql)->queryAll();
        if(isset($data[0]) && !empty($data[0]))
            return $data[0];
        else
            return 0;
    }

    public function getAnnouncementData($current_date){
        $sql    = "SELECT title, 
                          announcement_type, 
                          text_content, 
                          CASE WHEN display_type = 1 THEN 'Model Popup'
                               WHEN display_type = 2 THEN 'Information'
                               ELSE NULL END AS display_type, 
                          button_text, 
                          click_action, 
                          announcement_indicate,
                          display_icon,
                          display_text, 
                          CASE WHEN is_close_button = 1 THEN 'Yes' ELSE 'No' END AS is_close_button
                    FROM announcement_config
                    WHERE status = 1
                          AND CASE WHEN end_date IS NOT NULL THEN '".$current_date."' BETWEEN start_date AND end_date ELSE status = 1 END
                    ORDER BY id DESC";
        $data   = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

  public function createOutletDistributor($distrubutor_ids, $tse_id, $outlet_id){
      $flag = 0;
      $date = date('Y-m-d H:i:s');        
      $temp_distrubutor_ids = explode(',', $distrubutor_ids);
      for($i = 0 ; $i < count($temp_distrubutor_ids); $i++){
          $sql = "INSERT INTO tse_distributor_mapping(tse_id, distributor_id, createdon, enrolled_by, outlet_id) VALUES ($tse_id, $temp_distrubutor_ids[$i], '$date', $tse_id, $outlet_id)";
          $data   = Yii::$app->db->createCommand($sql)->queryAll();
          $flag   = 1;
      }
      
      if($flag == 1) {
          return true;
      } else {
          return false;    
      } 
  }


}?>