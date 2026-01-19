<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserMoneyApply */

$this->title = 'Create User Money Apply';
$this->params['breadcrumbs'][] = ['label' => 'User Money Applies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-money-apply-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
