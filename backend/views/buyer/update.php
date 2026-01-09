<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Buyer */

$this->title = 'Update Buyer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Buyers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="buyer-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
