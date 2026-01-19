<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%buyer}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $level_id
 * @property float|null $goods_money
 * @property float|null $money
 * @property string|null $code
 * @property string|null $title
 * @property string|null $province
 * @property string|null $city
 * @property string|null $area
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Buyer extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'province', 'city', 'area'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['money'], 'default', 'value' => 0.00],
            [['user_id', 'level_id', 'created_at', 'updated_at'], 'integer'],
            [['goods_money', 'money'], 'number'],
            [['code'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 100],
            [['province', 'city', 'area'], 'string', 'max' => 255],
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
            'level_id' => '等级',
            'title'=>'供货商名称',
            'goods_money' => '货款',
            'money' => '余额',
            'code' => 'Code',
            'province' => '省',
            'city' => '市',
            'area' => '区',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
        ];
    }

}
