<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property integer $bx_days
 * @property integer $lx_days
 * @property string $goods_code
 * @property string $goods_name
 * @property string $goods_image
 * @property string $goods_number
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bx_days', 'lx_days'], 'integer'],
            [['goods_code', 'goods_name', 'goods_image', 'goods_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bx_days' => '保险天数',
            'lx_days' => '滤芯天数',
            'goods_code' => '产品编号',
            'goods_name' => '产品名称',
            'goods_image' => '产品图片',
            'goods_number' => '产品型号',
        ];
    }

}
