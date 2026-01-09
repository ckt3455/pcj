<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuyerOrder */

$this->title = 'Update Buyer Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Buyer Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="buyer-order-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
