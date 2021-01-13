<?php
use yii\helpers\Html;	
use yii\bootstrap\ActiveForm;

use kartik\widgets\DatePicker;
// usage without model
echo '<label>Check Issue Date</label>';
echo DatePicker::widget([
    'name' => 'check_issue_date',
    'value' => date('d-M-Y', strtotime('+2 days')),
    'options' => ['placeholder' => 'Select issue date ...'],
    'pluginOptions' => [
        'format' => 'dd-M-yyyy',
        'todayHighlight' => true
    ]
]);
?>
<h1>Komentar</h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'nama') ?>
<?= $form->field($model, 'pesan') ?>
<?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>


<?php ActiveForm::end(); ?>