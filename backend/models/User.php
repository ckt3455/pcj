<?php

namespace backend\models;

use common\components\CommonFunction;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $id
 * @property string $mobile
 * @property string $password
 * @property string $code
 * @property string $parent_id
 * @property string $money
 * @property string $created_at
 * @property string $updated_at
 * @property string $is_buy
 * @property string $name
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }


    const JWT_ALGORITHM = 'RS256';
    const JWT_EXPIRE_TIME = 7200; // 2小时

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile'], 'required'],
            [['parent_id', 'created_at', 'updated_at', 'is_buy', 'level_time', 'level_time2', 'level_time3'], 'integer'],
            [['money'], 'number'],
            [['mobile', 'code'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 255],
            [['mobile'], 'unique', 'message' => '该号码已存在'],
            [['code'], 'unique', 'message' => '邀请码已存在'],
            ['mobile', 'match', 'pattern' => '/^1[3-9]\d{9}$/','message'=>'手机号格式不正确'],
            [['name', 'image', 'level_id', 'integral', 'dl_type', 'city', 'area'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号',
            'password' => '密码',
            'parent_id' => '直推用户',
            'money' => '余额',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
            'is_buy' => 'Is Buy',
            'image' => '头像',
            'name' => '名称',
            'level_id' => '用户等级',
            'integral' => '积分',
            'city' => '城市',
            'area' => '地区',
        ];
    }

    public function beforeSave($insert)

    {

        if($this->isNewRecord and !$this->password){
            $this->password=md5('123456'.md5(Yii::$app->params['password_code']));
        }else{
            if ($this->isAttributeChanged('password')) {


                $this->password = md5($this->password.md5(Yii::$app->params['password_code']));


            }

        }
        if($this->image){
            $this->image=CommonFunction::unsetImg($this->image);
        }



        return parent::beforeSave($insert);

    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }






    public function getParent(){
        return $this->hasOne(User::className(),['id'=>'parent_id']);
    }


    public static function getList(){
        $model=User::find()->asArray()->all();
        return ArrayHelper::map($model,'id','mobile');
    }


    public static function getList2(){
        $model=User::find()->asArray()->all();
        $arr=[];
        foreach ($model as $k=>$v){
            $arr[$v['id']]=$v['mobile'].'-'.$v['name'];
        }
        return $arr;
    }



    //关联用户等级
    public function  getLevel(){
        return $this->hasOne(UserLevel::className(),['id'=>'level_id']);
    }


    public function getLoginToken()
    {
        $payload = [
            'iat' => time(), // 签发时间
            'exp' => time() + self::JWT_EXPIRE_TIME, // 过期时间
            'user_id' => $this->id, // 用户ID
        ];

        $publicKey = Yii::$app->params['rsa_public'];
        if (strpos($publicKey, '-----BEGIN PUBLIC KEY-----') === false) {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
                chunk_split($publicKey, 64, "\n") .
                "-----END PUBLIC KEY-----";
        }
        $json = json_encode($payload);
        $encrypted = '';
        openssl_public_encrypt($json, $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING);
        return base64_encode($encrypted);
    }


    public static function decrypt(string $encrypted)
    {
        $privateKey = Yii::$app->params['rsa_private'];;
        if (strpos($privateKey, '-----BEGIN PRIVATE KEY-----') === false) {
            $privateKey = "-----BEGIN PRIVATE KEY-----\n" .
                chunk_split($privateKey, 64, "\n") .
                "-----END PRIVATE KEY-----";
        }
        $decrypted = '';
        openssl_private_decrypt(base64_decode($encrypted), $decrypted, $privateKey, OPENSSL_PKCS1_OAEP_PADDING);
        return json_decode($decrypted, true);
    }








}
