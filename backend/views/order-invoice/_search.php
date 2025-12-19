<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\search\OrderInvoiceSearch */
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

    <?= $form->field($model, 'user_id',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'order_id',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'email',['options'=>['tag'=>false]]) ?>

    <?= $form->field($model, 'type',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'order_number',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'title',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'number',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'company_name',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'phone',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'bank',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'bank_account',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'address',['options'=>['tag'=>false]]) ?>

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
                )->label('结束时间') ?>    <?php // echo  $form->field($model, 'address2',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'contact',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'mobile',['options'=>['tag'=>false]]) ?>

    <?php // echo  $form->field($model, 'status',['options'=>['tag'=>false]]) ?>

    <div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
