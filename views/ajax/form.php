

<?php 

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'options' => [
        'id' => 'formX',
    ],
    'action' => ['create'],
    'method' => 'post',
]); ?>
<?= $form->field($model, 'name')->textInput() ?>
<div class="form-group">
    <?= Html::submitButton('Add to list', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    $(document).on("beforeSubmit","#formX",function(event)     {
        var form = $(this);
        if (form.find(".has-error").length) {
            return false;
        }
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: form.serialize(),
            success: function (response) {
               alert("dasdas"),
            }
        });
        return false;
    });
');