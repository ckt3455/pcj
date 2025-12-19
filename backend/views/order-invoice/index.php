<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\OrderInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

                <?= Bar::widget()?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => false,
    'options' => ['class' => 'grid-view','style'=>'overflow:auto', 'id' => 'grid'],
        'columns' => [
    [
    'headerOptions' => ['width' => '20'],
    'class' => 'yii\grid\CheckboxColumn',
    'name' => 'id',
    ],
            ['class' => 'yii\grid\SerialColumn'],


[
'attribute'=>'user_id',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'order_id',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'email',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'type',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'order_number',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'title',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'number',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'company_name',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'phone',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'bank',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'bank_account',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'address',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'created_at',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'address2',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'contact',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'mobile',
'class'=>'kartik\grid\EditableColumn'
],
[
'attribute'=>'status',
'class'=>'kartik\grid\EditableColumn'
],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',
                'buttons'=>[
                      'update'=>function($url,$model,$key){
                        return "<a href='javascript:void(0);'  type=\"button\" class=\"btn btn-primary btn-sm\"  onclick=\"viewLayer('$url',$(this))\" data-pjax='0' > 编辑</a>";

                            },
                        'delete'=>function($url,$model,$key){
                         return "<a   type=\"button\" class=\"btn btn-warning btn-sm\"  href=\"$url\" data-method='post' data-pjax='0' data-confirm='确定要删除吗？'> 删除</a>";

    },
    ]


            ],
        ],
    'pager' =>[
    'class' => \common\components\GoPager::className(),
    'firstPageLabel' => '首页',
    'prevPageLabel' => '《',
    'nextPageLabel' => '》',
    'lastPageLabel' => '尾页',
    'goPageLabel' => true,
    'totalPageLable' => '共x页',
    'goButtonLable' => 'GO',
    'maxButtonCount' => 5
    ],
    ]); ?>
            </div>
        </div>
    </div>
</div>

