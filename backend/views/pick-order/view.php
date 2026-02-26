<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PickOrder */
/* @var $details backend\models\PickOrderDetail[] */

$this->title = '提货订单详情：' . $model->pick_number;
$this->params['breadcrumbs'][] = ['label' => '提货订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pick-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->status == 1): ?>
            <?= Html::a('审核', ['audit', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        <?php if ($model->status == 2): ?>
            <?= Html::a('确认提货', ['confirm-pick', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => ['method' => 'post', 'confirm' => '确定要确认提货吗？'],
            ]) ?>
        <?php endif; ?>
        <?php if (in_array($model->status, [1, 2])): ?>
            <?= Html::a('取消订单', ['cancel', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['method' => 'post', 'confirm' => '确定要取消该订单吗？'],
            ]) ?>
        <?php endif; ?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'pick_number',
            [
                'attribute' => 'user_id',
                'value' => $model->user ? $model->user->username : '-',
            ],
            'consignee',
            'phone',
            'province',
            'city',
            'area',
            'address_detail',
            [
                'attribute' => 'total_amount',
                'format' => 'decimal',
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusText(),
            ],
            'content:ntext',
            [
                'attribute' => 'created_at',
                'value' => date('Y-m-d H:i:s', $model->created_at),
            ],
            [
                'attribute' => 'audit_time',
                'value' => $model->audit_time ? date('Y-m-d H:i:s', $model->audit_time) : '-',
            ],
            [
                'attribute' => 'audit_user_id',
                'value' => $model->auditUser ? $model->auditUser->username : '-',
            ],
            [
                'attribute' => 'pick_time',
                'value' => $model->pick_time ? date('Y-m-d H:i:s', $model->pick_time) : '-',
            ],
        ],
    ]) ?>

    <h3>商品明细</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>商品名称</th>
                <th>规格</th>
                <th>单价</th>
                <th>数量</th>
                <th>小计</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $detail): ?>
            <tr>
                <td><?= Html::encode($detail->goods_name) ?></td>
                <td><?= Html::encode($detail->sku_name ?: '-') ?></td>
                <td>¥<?= number_format($detail->price, 2) ?></td>
                <td><?= $detail->quantity ?></td>
                <td>¥<?= number_format($detail->subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>总计：</strong></td>
                <td><strong>¥<?= number_format($model->total_amount, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>

</div>
