<?php
use miloschuman\highcharts\Highcharts;
echo "<h1>Sample Highchart</h1>";
echo Highcharts::widget([
    'scripts' => [
        'modules/exporting',
    ],
    'options' => [
        'title' => ['text' => 'Fruit Consumption'],
        'subtitle' => ['text' => 'Sub Judul Chart'],
        'chart' => [
            'type' => 'column',
        ],
        'series' => [
            ['type' => 'column','name' => 'Jane', 'data' => [1, 2, 4]],
            ['type' => 'line','name' => 'John', 'data' => [5, 7, 3]]
        
        ]
    ]
]);