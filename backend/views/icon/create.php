<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Icon */

$this->title = 'Create Icon';
$this->params['breadcrumbs'][] = ['label' => 'Icons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="icon-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
