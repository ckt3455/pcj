<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsCategory */

$this->title = 'Update Goods Category: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Goods Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-category-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
