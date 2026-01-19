<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserMoneyApply */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Money Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-money-apply-view">

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
            'user_id',
            'buyer_id',
            'money',
            'fee',
            'status',
            'type',
            'apply_type',
            'zfb_number',
            'zfb_name',
            'zfb_image',
            'wx_number',
            'wx_name',
            'wx_image',
            'bank_number',
            'bank_name',
            'bank_open',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
