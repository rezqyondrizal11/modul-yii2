<h1>Daftar Employee</h1>

<?php

use \yii\helpers\Url;
use \yii\helpers\Html;

echo Html::a('Create',['create'],['class'=>'btn btn-primary']);

echo"<br>";
echo"<br>";
echo "<table class='table table-bordered table-striped'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>NAME</th>";
echo "<th>AGE</th>";
echo "<th>ACTION</th>";
echo "</tr>";
foreach($employees as $employee){
    echo "<tr>";
    echo "<td>".$employee->id."</td>";
    echo "<td>".$employee->name."</td>";
    echo "<td>".$employee->age."</td>";
    echo "<td>";
    echo Html::a('<i class="glyphicon glyphicon-pencil">&nbsp   </i>',['employee/update','id'=>$employee->id]);
    echo Html::a('<i class="glyphicon glyphicon-trash"></i>',['employee/delete','id'=>$employee->id],
    ['onclick'=>'return (confirm("Apakah data mau dihapus?")?true:false);']);
    

    echo "</td>";
    echo "</tr>";
}
echo "</table>";

