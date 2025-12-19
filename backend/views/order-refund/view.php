<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderRefund */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-refund-view">

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
            'user_id',
            'order_number',
            'contact',
            'mobile',
            'type',
            'message',
            'content',
            'created_at',
            'detail_id',
            'image',
            'money',
            'freight',
            'status',
        ],
    ]) ?>

</div>
