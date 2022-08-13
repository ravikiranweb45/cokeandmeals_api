<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property string $createdon
 * @property string $fromid
 * @property string $toid
 * @property string $sub
 * @property string $body
 * @property integer $msgtype
 * @property string $senton
 * @property integer $status
 * @property integer $campaign_id
 * @property integer $program_id
 * @property string $template_id
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createdon', 'toid', 'body', 'msgtype', 'status'], 'required'],
            [['createdon', 'senton'], 'safe'],
            [['msgtype', 'status','campaign_id'], 'integer'],
            [['fromid', 'toid'], 'string', 'max' => 50],
            [['sub','template_id'], 'string', 'max' => 250],
            //[['body'], 'string', 'max' => 5000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createdon' => 'Created On',
            'fromid' => 'From ID',
            'toid' => 'To ID',
            'sub' => 'Sub',
            'body' => 'Body',
            'msgtype' => 'Msgtype',
            'senton' => 'Sent On',
            'status' => 'Status',
        ];
    }
	
	public function sendEmail($sub, $msg, $to,$campaign_id,$program_id)
	{
        date_default_timezone_set('Asia/Kolkata');
        $this->campaign_id = 1;
        $this->program_id = 1;
		$this->createdon = date('Y-m-d H:i:s');
		$this->fromid = Yii::$app->params['adminEmail'];
		$this->toid = $to;
		$this->body = $msg;
		$this->msgtype = 1; // email
		$this->sub = $sub;
		$this->status = 0; // 0 is new, 5 and more for sent, less than 0 for failed.
		return $this->save();
	}
	
	public function sendSMS($to,$msg,$sub,$template_flag='')
	{
        date_default_timezone_set('Asia/Kolkata');
	    $this->createdon = date('Y-m-d H:i:s');
		$this->fromid = 'BIGCITY';
		$this->toid = $to;
		$this->body = $msg;
		$this->msgtype = 2; // SMS
		$this->sub = $sub;
		$this->status = 0; // 0 is new, 5 and more for sent, less than 0 for failed.
		$template_id       = $this->getMessageTempalateId($template_flag);
        $this->template_id = $template_id;
		$this->save();
	}

	public function getMessageTempalateId($template_flag){
        if(isset($template_flag) && !empty($template_flag)){
            if($template_flag == 'gen_otp_sms'){
                $template_id = '1707160404740698653';
            }
            else{
                $template_id = '8888888888';
            }
        }
        else{
            $template_id = '9999999999';
        }
        return $template_id;
    }	

    public function otpWithinTwoMins($phone_num){
        // 0 is new, 5 and more for sent, less than 0 for failed.
        $sql = "select * from messages where toid = '$phone_num' and msgtype = 2 and status = 0 and createdon > NOW() - INTERVAL '2 minutes'";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

}
