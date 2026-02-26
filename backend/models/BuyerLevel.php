<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%buyer_level}}".
 *
 * @property int $id
 * @property int|null $level
 * @property string|null $title
 * @property string|null $image
 * @property string|null $content
 * @property float|null $number
 */
class BuyerLevel extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer_level}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'image', 'content'], 'default', 'value' => null],
            [['level'], 'default', 'value' => 1],
            [['number'], 'default', 'value' => 0.00],
            [['level'], 'integer'],
            [['number'], 'number'],
            [['title', 'image', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => '等级',
            'title' => '名称',
            'image' => '图片',
            'content' => '说明',
            'number' => '拿货折扣',
        ];
    }

    public static function level_name($id)
    {
        $level = self::findOne($id);
        return $level['title'];

    }

}
