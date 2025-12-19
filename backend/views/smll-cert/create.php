<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\SmallCert */

$this->title = 'Create Small Cert';
$this->params['breadcrumbs'][] = ['label' => 'Small Certs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="small-cert-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
