<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\OrderInvoice */

$this->title = 'Create Order Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Order Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-invoice-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
