<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'sub_title',
            'thumb',
            'thumbs:ntext',
            'thumb_video',
            'category_id',
            'has_option',
            'price',
            'crossed_price',
            'sales',
            'content:ntext',
            'status',
            'sort',
            'upc_code',
            'intro',
            'weight',
            'units',
            'stock',
            'stock_warning',
            'score',
            'hot',
            'associated_goods:ntext',
            'freight_model_id',
            'append',
            'updated',
            'is_del',
        ],
    ]) ?>

</div>
