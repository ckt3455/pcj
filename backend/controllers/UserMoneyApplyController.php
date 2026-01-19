<?php

namespace backend\controllers;

use Yii;
use backend\search\UserMoneyApplySearch;
use backend\models\UserMoneyApply;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

/**
 * UserMoneyApplyController implements the CRUD actions for UserMoneyApply model.
 */
class UserMoneyApplyController extends MController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => UserMoneyApply::className(),
                'data' => function(){
                    
                        $searchModel = new UserMoneyApplySearch();
                        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                        return [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                        ];
                    
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => UserMoneyApply::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => UserMoneyApply::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => UserMoneyApply::className(),
            ],
        ];
    }
}
