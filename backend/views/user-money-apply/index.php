<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\UserMoneyApplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Money Applies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">

                <?php echo $this->render('_search', ['model' => $searchModel]); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'export' => false,
                    'options' => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
                    'columns' => [
                        [
                            'headerOptions' => ['width' => '20'],
                            'class' => 'yii\grid\CheckboxColumn',
                            'name' => 'id',
                        ],
                        ['class' => 'yii\grid\SerialColumn'],


                        [
                            'attribute' => 'user_id',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'buyer_id',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'money',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'fee',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'status',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'type',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'apply_type',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'zfb_number',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'zfb_name',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'zfb_image',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'wx_number',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'wx_name',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'wx_image',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'bank_number',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'bank_name',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'bank_open',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        'created_at:datetime',
                        ['class' => 'yii\grid\ActionColumn', 'template' => '',
                            'buttons' => [

                            ]


                        ],
                    ],
                    'pager' => [
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

