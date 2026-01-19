<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_money_apply}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $buyer_id
 * @property float|null $money
 * @property float|null $fee
 * @property int|null $status
 * @property int|null $type
 * @property int|null $apply_type
 * @property string|null $zfb_number
 * @property string|null $zfb_name
 * @property string|null $zfb_image
 * @property string|null $wx_number
 * @property string|null $wx_name
 * @property string|null $wx_image
 * @property string|null $bank_number
 * @property string|null $bank_name
 * @property string|null $bank_open
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserMoneyApply extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_money_apply}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money', 'fee', 'zfb_number', 'zfb_name', 'zfb_image', 'wx_number', 'wx_name', 'wx_image', 'bank_number', 'bank_name', 'bank_open'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['apply_type'], 'default', 'value' => 1],
            [['user_id', 'buyer_id', 'status', 'type', 'apply_type', 'created_at', 'updated_at'], 'integer'],
            [['money', 'fee'], 'number'],
            [['zfb_number', 'zfb_name', 'wx_number', 'wx_name'], 'string', 'max' => 100],
            [['zfb_image', 'wx_image', 'bank_number', 'bank_name', 'bank_open'], 'string', 'max' => 255],
        ];
    }


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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'buyer_id' => '订货商',
            'money' => '金额',
            'fee' => '手续费',
            'status' => '状态',
            'type' => '类型',
            'apply_type' => '提现方式',
            'zfb_number' => '支付宝账户',
            'zfb_name' => '支付宝名称',
            'zfb_image' => '支付宝二维码',
            'wx_number' => '微信账号',
            'wx_name' => '微信名称',
            'wx_image' => '微信二维码',
            'bank_number' => '银行卡号',
            'bank_name' => '银行账户名',
            'bank_open' => '开户行',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }

}
