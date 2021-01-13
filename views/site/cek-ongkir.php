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
<?php
if(!empty($results)){
    echo "<table class='table'>";
    foreach($results as $result) {
        //print_r($result->query);
        //print_r($result->status);
        //print_r($result->origin_details);
        //print_r($result->results);
        foreach ($result->results[0]->costs as $costs) {
            echo '<tr><th>service</th><th>:</th><th>' . $costs->service . '</th></tr>';
            echo '<tr><td>description</td><td>:</td><td>' . $costs->description . '</td></tr>';
            echo '<tr><td>cost</td><td>:</td><td>' . $costs->cost[0]->value . '</td></tr>';
            echo '<tr><td>etd</td><td>:</td><td>' . $costs->cost[0]->etd. '</td></tr>';
        }
    }
}
?>
<?php ActiveForm::end(); ?>
</div>
