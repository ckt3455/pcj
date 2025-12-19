<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\search\OrderRefundSearch */
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


    <?= $form->field($model, 'order_number',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'contact',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'mobile',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'type',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'message',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'content',['options'=>['tag'=>false]]) ?>

       <?= $form->field($model, 'start_time', ['options' => ['tag' => false]])->widget(
                    \kartik\datetime\DateTimePicker::className(), [
                        'pluginOptions' => [
                            'language' => 'zh-CN',
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'todayBtn' => true,
                            'minView' => 'month',

                        ]]
                )->label('开始时间') ?>       <?= $form->field($model, 'end_time', ['options' => ['tag' => false]])->widget(
                    \kartik\datetime\DateTimePicker::className(), [
                        'pluginOptions' => [
                            'language' => 'zh-CN',
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'todayBtn' => true,
                            'minView' => 'month',

                        ]]
                )->label('结束时间') ?>    <?php // echo  $form->field($model, 'detail_id',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'image',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'money',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'freight',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'status',['options'=>['tag'=>false]]) ?>

    <div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
