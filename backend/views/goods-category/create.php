<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\GoodsCategory */

$this->title = 'Create Goods Category';
$this->params['breadcrumbs'][] = ['label' => 'Goods Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-category-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
