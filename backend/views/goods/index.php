<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">

                <?php echo $this->render('_search', ['model' => $searchModel]); ?>

                <?= Bar::widget() ?>    <?= GridView::widget([
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
                            'attribute' => 'title',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'sub_title',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'thumb',
                            'format' => 'html',
                            'value' => function ($data) {
                                if ($data->thumb) {
                                    return "<img class='show_image' style='width: 100px;height: 100px' src='$data->thumb'>";
                                }
                            }
                        ],

                        [
                            'attribute' => 'category_id',
                            'value' => function ($data) {
                                if($data->category){
                                    return $data->category['title'];
                                }
                            }
                        ],

                        [
                            'attribute' => 'price',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'crossed_price',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'sales',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($data) {
                                return \backend\models\Goods::$status[$data->status];
                            }
                        ],
                        [
                            'attribute' => 'sort',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'upc_code',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'stock',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        [
                            'attribute' => 'stock_warning',
                            'class' => 'kartik\grid\EditableColumn'
                        ],
                        'hot',

                        'append:datetime',
                        ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',
                            'buttons' => [
                                'update' => function ($url, $model, $key) {
                                    return "<a href='javascript:void(0);'  type=\"button\" class=\"btn btn-primary btn-sm\"  onclick=\"viewLayer('$url',$(this))\" data-pjax='0' > 编辑</a>";

                                },
                                'delete' => function ($url, $model, $key) {
                                    return "<a   type=\"button\" class=\"btn btn-warning btn-sm\"  href=\"$url\" data-method='post' data-pjax='0' data-confirm='确定要删除吗？'> 删除</a>";

                                },
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

