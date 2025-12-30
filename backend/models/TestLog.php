<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "zs_test_log".
 *
 * @property int $id
 * @property string|null $content
 * @property int|null $created_at
 */
class TestLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zs_test_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['created_at'], 'default', 'value' => 0],
            [['created_at'], 'integer'],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '内容',
            'created_at' => '时间',
            'type' => '类型',
        ];
    }

}
