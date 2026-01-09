<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuyerLevel */

$this->title = 'Update Buyer Level: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Buyer Levels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="buyer-level-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
