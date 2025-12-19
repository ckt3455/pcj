<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderRefund */

$this->title = 'Update Order Refund: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-refund-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
