<?php

namespace backend\controllers;

use Yii;
use backend\search\BuyerLevelSearch;
use backend\models\BuyerLevel;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * BuyerLevelController implements the CRUD actions for BuyerLevel model.
 */
class BuyerLevelController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => BuyerLevel::className(),
                'data' => function(){
                    
                        $searchModel = new BuyerLevelSearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => BuyerLevel::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => BuyerLevel::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => BuyerLevel::className(),
            ],
        ];
    }
}
