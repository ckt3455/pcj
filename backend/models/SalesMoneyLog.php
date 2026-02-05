<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%sales_money_log}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property float|null $number
 * @property int|null $type
 * @property string|null $content
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $sales_id
 */
class SalesMoneyLog extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),

        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sales_money_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['sales_id'], 'default', 'value' => 0],
            [['number'], 'default', 'value' => 0.00],
            [['type'], 'default', 'value' => 1],
            [['user_id', 'type', 'created_at', 'updated_at', 'sales_id'], 'integer'],
            [['number'], 'number'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    public static $type_message=[
        1=>'订单返佣',
        2=>'提现扣减'
    ];



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'number' => '金额',
            'type' => '类型',
            'content' => '内容',
            'created_at' => '时间',
            'updated_at' => 'Updated At',
            'sales_id' => '分销商',
        ];
    }

}
