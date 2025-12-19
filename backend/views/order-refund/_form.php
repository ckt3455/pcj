<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderRefund */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-refund-form">

    <?php $form = ActiveForm::begin(); ?>




    <?= $form->field($model, 'status')->dropDownList(\backend\models\OrderRefund::$status_message) ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
