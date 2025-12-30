<?php

namespace api\controllers;

use common\controllers\BaseController;
use Yii;
use backend\search\TestLogSearch;
use backend\models\TestLog;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use yii\web\Controller;

/**
 * TestLogController implements the CRUD actions for TestLog model.
 */
class TestLogController extends Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => TestLog::className(),
                'data' => function(){

                        $this->layout=false;

                        $searchModel = new TestLogSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];

                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => TestLog::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => TestLog::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => TestLog::className(),
            ],
        ];
    }
}
