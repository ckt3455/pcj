<?php

namespace backend\controllers;

use Yii;
use backend\search\IconSearch;
use backend\models\Icon;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * IconController implements the CRUD actions for Icon model.
 */
class IconController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Icon::className(),
                'data' => function(){
                    
                        $searchModel = new IconSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Icon::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Icon::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Icon::className(),
            ],
        ];
    }
}
