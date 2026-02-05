<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%sales}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $level
 * @property float|null $money
 * @property string|null $code
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string|null $title
 * @property float|null $get_money
 * @property string|null $name
 * @property string|null $image
 */
class Sales extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sales}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'title', 'get_money', 'name', 'image'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['money'], 'default', 'value' => 0.00],
            [['user_id', 'level', 'created_at', 'updated_at'], 'integer'],
            [['money', 'get_money'], 'number'],
            [['code'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 100],
            [['name', 'image'], 'string', 'max' => 255],
        ];
    }

    public static $level_message=[
        1=>'1级分销商',
        2=>'2级分销商'
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'level' => '等级',
            'money' => '余额',
            'code' => 'Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'title' => 'Title',
            'get_money' => '总获得金额',
            'name' => 'Name',
            'image' => 'Image',
        ];
    }

}
