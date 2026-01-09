<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%buyer_relation}}".
 *
 * @property int $id
 * @property int|null $buyer_id
 * @property int|null $level
 * @property string|null $relation
 * @property int|null $parent_id
 */
class BuyerRelation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => 0],
            [['relation'], 'default', 'value' => ''],
            [['buyer_id', 'level', 'parent_id'], 'integer'],
            [['relation'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buyer_id' => 'Buyer ID',
            'level' => 'Level',
            'relation' => 'Relation',
            'parent_id' => 'Parent ID',
        ];
    }

}
