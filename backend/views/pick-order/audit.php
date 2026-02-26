<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\PickOrder;

/* @var $this yii\web\View */
/* @var $model backend\models\PickOrder */

$this->title = '审核提货订单：' . $model->pick_number;
$this->params['breadcrumbs'][] = ['label' => '提货订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pick_number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '审核';
?>

<div class="pick-order-audit">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="pick-order-form">

        <?php $form = ActiveForm::begin(['method' => 'post']); ?>

        <div class="form-group">
            <label class="control-label">审核结果</label>
            <select name="status" class="form-control" required>
                <option value="">请选择</option>
                <option value="2">✓ 通过（待提货）</option>
                <option value="4">✗ 拒绝（已取消）</option>
            </select>
        </div>

        <div class="form-group">
            <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
            <?= Html::a('取消', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
