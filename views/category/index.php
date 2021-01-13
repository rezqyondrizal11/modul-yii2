<?php
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use hscstudio\mimin\components\Mimin;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'id' => 'categoryModal',
]);
    Pjax::begin([
        'id'=>'pjax-modal','timeout'=>false,
        'enablePushState'=>false,
        'enableReplaceState'=>false,
    ]);

    Pjax::end();
Modal::end();
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if ((Mimin::checkRoute($this->context->id.'/create'))){
        echo Html::a('Create Category', ['create'], ['class' => 'btn btn-success']);
        } 
        ?>
        
    </p>
    <?= Html::a('Export Excel', ['export-excel'], ['class'=>'btn btn-info']); ?>  
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php \yii\widgets\Pjax::begin(['timeout'=>false, 'id'=>'gridview']); ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',

            
            // ['class' => 'yii\grid\ActionColumn',
            // 'buttons' => [
            //     'view' => function ($url, $model) {
            //         $icon='<span class="glyphicon glyphicon-eye-open"></span>';
            //         return Html::a($icon,$url,[
            //             'data-toggle'=>"modal",
            //             'data-target'=>"#categoryModal",
            //         ]);
            //     },
                
            //     'update' => function ($url, $model) {
            //         $icon='<span class="glyphicon glyphicon-pencil"></span>';
            //         return Html::a($icon,$url,[
            //             'data-toggle'=>"modal",
            //             'data-target'=>"#categoryModal",
            //         ]);
            //     },
            //     'delete' => function ($url, $model) {
            //         $icon='<span class="glyphicon glyphicon-trash"></span>';
            //         return Html::a($icon,$url,[
            //             // 'data-confirm'=>"Are you sure you want to delete this item?",
            //             // 'data-method'=>'post',
            //             'class'=>'pjaxDelete'

            //         ]);
            //     },

            // ]
            // ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => Mimin::filterActionColumn([
                    'view','update','delete'
                ],$this->context->route),
               
            ],
          
        ],
    ]); 
    $this->registerJs('
    /* fungsi ini akan dijalankan ketika class pjaxDelete diklik */
    $(".pjaxDelete").on("click", function (e) {
        /* cegah tautan menjalankan default action */
        e.preventDefault();
        if(confirm("Are you sure you want to delete this item?")){
            /* request actionDelete dengan method post */
            $.post($(this).attr("href"), function(data) {
                /* reload gridview */
                $.pjax.reload("#gridview",{"timeout":false});
            });
        }
    });

    $("#categoryModal").on("shown.bs.modal", function (event) {
        var button = $(event.relatedTarget)
        var href = button.attr("href")
        $.pjax.reload("#pjax-modal",{
            "timeout":false,
            "url": href, 
            "replace": false,
        });
    })


');

$this->registerJs('
    var currentData = "";
    var check = function(){
        setTimeout(function(){
            $.ajax({ url: "'.Url::to(['category/check']).'", success: function(data){
                if(currentData!=data.lastId){
                    currentData = data.lastId;    
                    $.pjax({
                        url:"'.Url::to(['category/index']).'",
                        container:"#gridview",
                        timeout:false,
                        replace: false,
                    }).done(function(data) { 
                        check();
                    });
                }
                else{
                    check();
                }
            }, dataType: "json"});
        }, 5000);
    }
    check();
');
?>

        <?php \yii\widgets\Pjax::end() ?>
</div>
