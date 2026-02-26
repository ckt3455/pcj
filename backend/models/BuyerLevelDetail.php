<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%buyer_level_detail}}".
 *
 * @property int $id
 * @property int|null $level_id
 * @property string|null $title
 * @property string|null $content
 * @property int|null $type
 * @property float|null $number
 */
class BuyerLevelDetail extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer_level_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'default', 'value' => null],
            [['level_id'], 'default', 'value' => 1],
            [['type'], 'default', 'value' => 0],
            [['number'], 'default', 'value' => 0.00],
            [['level_id', 'type'], 'integer'],
            [['number'], 'number'],
            [['title', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level_id' => 'Level ID',
            'title' => 'Title',
            'content' => 'Content',
            'type' => 'Type',
            'number' => 'Number',
        ];
    }

}
