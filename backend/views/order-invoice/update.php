<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInvoice */

$this->title = 'Update Order Invoice: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Order Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-invoice-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
