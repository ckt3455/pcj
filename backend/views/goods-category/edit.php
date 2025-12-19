<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;


$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '产品分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <?= $form->field($model, 'parent_id')->hiddenInput()->label(false);?>



                <?= $form->field($model, 'title')->textInput() ?>



                <?= $form->field($model, 'sort')->textInput() ?>




                <?= $form->field($model, 'image')->widget('backend\widgets\webuploader\Image', [
                    'boxId' => 'image',
                    'options' => [
                        'multiple' => false,
                    ]
                ]) ?>


                <?= $form->field($model, 'content')->textarea(['row' => 20, 'style' => 'height:300px']); ?>



            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <button class="btn btn-primary" type="submit">保存内容</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>











