<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<h1>Gallery</h1>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <br>
    <br>
    <?= $form->field($model, 'imageFiles[]')->fileInput() ?>
    <?= $form->field($model, 'imageFiles[]')->fileInput() ?>
    <?= $form->field($model, 'imageFiles[]')->fileInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    


    <?php
foreach($model3 as $file){
    echo Html::img(Yii::getAlias('@web').'/uploads/'.$file->imageFiles,[
        'class'=>'img-thumbnail','style'=>'float:left;width:150px;'
    ]);
}
    ?>
<?php

ActiveForm::end();