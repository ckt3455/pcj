<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TestLog */

$this->title = 'Update Test Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Test Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="test-log-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
