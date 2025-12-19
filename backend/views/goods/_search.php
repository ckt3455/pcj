<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\search\GoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>查询</h5>
            </div>
            <div class="ibox-content">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
                'options'=>['class'=>'form-horizontal'],
                'fieldConfig'=> [
                'template' =>"<div class='col-sm-4 col-xs-6'> <div class='input-group m-b'> <div class='input-group-btn'>{label}</div>{input}</div></div>",
                'labelOptions' => ['class' => 'btn btn-primary'],
                ]

    ]); ?>

    <?= $form->field($model, 'id',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'title',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'sub_title',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'thumb',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'thumbs',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'thumb_video',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'category_id',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'has_option',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'price',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'crossed_price',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'sales',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'content',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'status',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'sort',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'upc_code',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'intro',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'weight',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'units',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'stock',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'stock_warning',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'score',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'hot',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'associated_goods',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'freight_model_id',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'append',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'updated',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'is_del',['options'=>['tag'=>false]]) ?>

    <div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
