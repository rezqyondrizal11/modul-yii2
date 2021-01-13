<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\models\City;
use yii\grid\GridView;
?>

<div class="cek-ongkir-form">
<?php $form = ActiveForm::begin(); ?>

<?php
$data = ArrayHelper::map(
    City::find()->select(['id','name'])
        ->asArray()->all(),
    'id', 'name');

echo $form->field($model, 'origin')->widget(Select2::classname(), [
    'options' => ['placeholder' => 'Select for a city ...'],
    'data'=>$data,
]);

echo $form->field($model, 'destination')->widget(Select2::classname(), [
    'options' => ['placeholder' => 'Select for a city ...'],
    'data'=>$data,
]);

?>
<?= $form->field($model, 'destination')->textInput() ?>
<?= $form->field($model, 'weight')->textInput(['value'=>1000]) ?>
<?= $form->field($model, 'courier')->dropDownList([
    'jne'=>'JNE',
    'pos'=>'POS',
    'tiki'=>'TIKI',
]) ?>
<div class="form-group">
    <?= Html::submitButton('Cek Ongkir', [
        'class' => 'btn btn-success'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
