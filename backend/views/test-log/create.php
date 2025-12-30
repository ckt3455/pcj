<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\TestLog */

$this->title = 'Create Test Log';
$this->params['breadcrumbs'][] = ['label' => 'Test Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-log-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
