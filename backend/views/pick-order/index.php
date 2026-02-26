<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\PickOrder;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\PickOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提货订单管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pick-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pick_number',
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user ? $model->user->username : '-';
                },
            ],
            'consignee',
            'phone',
            [
                'attribute' => 'total_amount',
                'format' => 'decimal',
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusText();
                },
                'filter' => PickOrder::$status,
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {audit} {confirm-pick} {cancel}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('查看', $url, ['class' => 'btn btn-sm btn-info']);
                    },
                    'audit' => function ($url, $model) {
                        if ($model->status == 1) {
                            return Html::a('审核', $url, ['class' => 'btn btn-sm btn-warning']);
                        }
                        return '';
                    },
                    'confirm-pick' => function ($url, $model) {
                        if ($model->status == 2) {
                            return Html::a('确认提货', $url, [
                                'class' => 'btn btn-sm btn-success',
                                'data' => ['method' => 'post'],
                            ]);
                        }
                        return '';
                    },
                    'cancel' => function ($url, $model) {
                        if (in_array($model->status, [1, 2])) {
                            return Html::a('取消', $url, [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => ['method' => 'post', 'confirm' => '确定要取消该订单吗？'],
                            ]);
                        }
                        return '';
                    },
                ],
            ],
        ],
    ]); ?>

</div>
