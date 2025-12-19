<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\OrderRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Refunds';
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
                            'attribute' => 'user_id',
                            'value' => function ($data) {
                                return $data->user['name'];
                            }
                        ],
                        'order_number',
                        'contact',
                        'mobile',
                        [
                            'attribute' => 'type',
                            'value' => function ($data) {
                                return \backend\models\OrderRefund::$type_message[$data->type];
                            }
                        ],
                        'message',
                        'content',
                        [
                            'attribute' => 'image',
                            'format' => 'html',
                            'value' => function ($data) {
                                if ($data->image) {
                                    $arr = explode(',', $data->image);
                                    $html = '';
                                    foreach ($arr as $k => $v) {
                                        $html .= "<img class='show_image' style='width: 100px;height: 100px' src='$v'>";
                                    }
                                    return $html;
                                }
                            }
                        ],
                        'money',
                        [
                            'attribute' => 'type',
                            'value' => function ($data) {
                                return \backend\models\OrderRefund::$status_message[$data->status];
                            }
                        ],
                        'created_at:datetime',
                        ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {finish}  {delete}',
                            'buttons' => [
                                'update' => function ($url, $model, $key) {

                                    if ($model->status == 1) {
                                        return "<a href='javascript:void(0);'  type=\"button\" class=\"btn btn-primary btn-sm\"  onclick=\"viewLayer('$url',$(this))\" data-pjax='0' > 审核</a>";

                                    }


                                },

                                'finish' => function ($url, $model, $key) {

                                    if ($model->status == 2) {

                                        return "<a   type=\"button\" class=\"btn btn-primary btn-sm\"   href='" . \yii\helpers\Url::to(['finish', 'id' => $model->id]) . "' data-method='post' data-pjax='0' data-confirm='确认完成吗？'> 确认完成</a>";


                                    }
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

<script>

    $('.show_image').click(function () {

        var image = $(this)[0].src;

        layer.open({

            type: 1,

            skin: 'layui-layer-demo', //样式类名

            closeBtn: 1, //不显示关闭按钮

            anim: 2,

            shadeClose: true, //开启遮罩关闭

            area: ['60%', '80%'],

            content: '<img style="width: 500px;height: 600px;text-align:center" src="' + image + '">'

        });

    })

</script>

