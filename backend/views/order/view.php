<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'order_number',
            'user_id',
            'consignee',
            'phone',
            'address_id',
            'address_detail',
            'invoice_id',
            'express',
            'total_price',
            'pay_method',
            'pay_status',
            'status',
            'content:ntext',
            'append',
            'updated',
            'paid_time:datetime',
            'delivery_time:datetime',
            'confirm_time:datetime',
            'is_delete',
            'category',
            'freight',
        ],
    ]) ?>

</div>
