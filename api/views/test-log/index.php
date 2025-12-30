<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\TestLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">


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
                        'type',
                        'content',
                        'created_at:datetime',
                        'ip',
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

