<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
Pjax::begin([
    'id'=>'pjax-form','timeout'=>false,
]);

?>

<!-- <div class="category-form"> -->
    <?php
    if(yii::$app->request->isAjax)
    echo \app\widgets\Alert::widget();
    ?>

   <?php 
   $form = ActiveForm::begin([
        'options'=>['data-pjax'=> true ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' =>
        $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a ($model->isNewRecord ? 'back' : 'close', ['index'], ['class' => 
         $model->isNewRecord ? 'btn btn-success' : 'btn btn-success',
         $model->isNewRecord ? 'redirect("index")':  
        'onclick'=>'
             $("#categoryModal") .modal("hide");
             return false; '
             
         ]
         
         ) ?>
        
    </div>

    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
<!-- </div> -->
<?php
$this->registerJs('
    $("#pjax-form") .on("pjax:end", function() {
        $.pjax.reload("#pjax-gridview",{
            "timeout": false,
            "url": "'. \yii\helpers\Url::to(['index']).'",
            "replace": false,
        });
    });    
');

