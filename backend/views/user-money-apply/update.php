<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserMoneyApply */

$this->title = 'Update User Money Apply: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Money Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-money-apply-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
