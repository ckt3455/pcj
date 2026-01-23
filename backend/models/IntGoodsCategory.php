<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%int_goods_category}}".
 *
 * @property int $id
 * @property string|null $title
 * @property float|null $min
 * @property float|null $max
 * @property int|null $sort
 */
class IntGoodsCategory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%int_goods_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'min', 'max'], 'default', 'value' => null],
            [['sort'], 'default', 'value' => 0],
            [['min', 'max'], 'number'],
            [['sort'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'min' => 'Min',
            'max' => 'Max',
            'sort' => 'Sort',
        ];
    }

}
