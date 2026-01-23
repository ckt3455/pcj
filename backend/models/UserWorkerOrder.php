<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_worker_order}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $goods_id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $image
 * @property int|null $status
 * @property string|null $order_number
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserWorkerOrder extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_worker_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'image', 'order_number'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
            [['user_id', 'goods_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'image'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 1000],
            [['order_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'goods_id' => 'Goods ID',
            'title' => 'Title',
            'content' => 'Content',
            'image' => 'Image',
            'status' => 'Status',
            'order_number' => 'Order Number',
        ];
    }


    public function behaviors()
    {
        return [
            TimestampBehavior::className(),

        ];
    }



}
