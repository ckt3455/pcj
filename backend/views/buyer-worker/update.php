<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuyerWorker */

$this->title = 'Update Buyer Worker: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Buyer Workers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="buyer-worker-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
