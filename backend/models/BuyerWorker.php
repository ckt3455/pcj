<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%buyer_worker}}".
 *
 * @property int $id
 * @property int|null $buyer_id
 * @property float|null $sales_money
 * @property float|null $buyer_money
 * @property float|null $get_money
 * @property int|null $user_id
 * @property float|null $money
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class BuyerWorker extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer_worker}}';
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
    public function rules()
    {
        return [
            [['money'], 'default', 'value' => 0.00],
            [['buyer_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['sales_money', 'buyer_money', 'get_money', 'money'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buyer_id' => '所属订货商',
            'sales_money' => '销售金额',
            'buyer_money' => '供货商获得金额',
            'get_money' => '获得金额',
            'user_id' => '用户',
            'money' => '余额',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }

}
