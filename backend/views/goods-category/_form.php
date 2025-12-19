<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->radioList([1=>'类型1',2=>'类型2',3=>'类型3']) ?>
    <?= $form->field($model, 'parent_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'image')->widget('backend\widgets\webuploader\Image', [
        'boxId' => 'image',
        'options' => [
            'multiple' => false,
        ]
    ]) ?>

    <?= $form->field($model, 'image2')->widget('backend\widgets\webuploader\Image', [
        'boxId' => 'image2',
        'options' => [
            'multiple' => false,
        ]
    ]) ?>

    <?= $form->field($model, 'content')->textarea(['row'=>5]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
