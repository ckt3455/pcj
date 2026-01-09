<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\BuyerOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Buyer Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-order-view">

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
            'buyer_id',
            'user_id',
            'type',
            'status',
            'pay_type',
            'order_number',
            'money',
            'discount',
            'total_money',
            'created_at',
            'updated_at',
            'paid_time:datetime',
            'parent_id',
            'level',
            'audit_time:datetime',
        ],
    ]) ?>

</div>
