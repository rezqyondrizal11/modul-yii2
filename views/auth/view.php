<?php
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
     
        ],
    ]) ?>
<table class="table table-striped">
    <tr>
        <th>Modules</th>
        <th>Controllers</th>
        <th>Actions</th>
        <th>Auth</th>
    </tr>
    <?php
    foreach($routes as $row) {
        ?>
        <tr >
            <td><?= $row['module']?></td >
            <td><?= $row['controller']?></td >
            <td><?= $row['action']?></td >
            <td><?= Html::checkbox('auth[]',$row['auth'],[
            'class' => 'processAuth',
    'data-module' => $row['module'],
    'data-controller' => $row['controller'],
    'data-action' => $row['action'],
]); ?></td>
        </tr >
        <?php
    }
    ?>
</table>
</div>
<?php
$this->registerJs('
    $(".processAuth").on("click", function (e) {
        module = $(this).attr("data-module");
        controller = $(this).attr("data-controller");
        action = $(this).attr("data-action");
        user_id = '.$model->id.';

        checked = $(this).prop("checked");

        if (checked){        
        var link = "'.Url::to(['process-auth']).'?module="+module+
            "&controller="+controller+"&action="+action+"&user_id="+user_id
        $.get(link, function(data) {
            alert(data)
        });
    } else {
        var link = "'.Url::to(['delete-auth']).'?module="+module+
            "&controller="+controller+"&action="+action+"&user_id="+user_id
        $.get(link, function(data) {
            alert(data)
        });}

        
    });
');
?>

