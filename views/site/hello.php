<?php

use \yii\helpers\Url;
use \yii\helpers\Html;

echo Html::a('Example','http://example.com');
echo "<br>";
echo Html::a('Data Person',['person/index']);
?>
<br>
<a href="<?= Url::to(['person/index']) ?>">Data Person</a> <br>