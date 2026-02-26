<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Icon */

$this->title = 'Update Icon: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Icons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="icon-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
