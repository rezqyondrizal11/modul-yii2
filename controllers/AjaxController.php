<?php

namespace app\controllers;

class AjaxController extends \yii\web\Controller
{

    public function getBooks()
    {
        $books = [
            ['id'=>'1','title'=>'Pemrograman PHP','author'=>'Hafid','year'=>'2015'],
            ['id'=>'2','title'=>'Pemrograman JS','author'=>'Juned','year'=>'2014'],
            ['id'=>'3','title'=>'Database MySQL','author'=>'Lily','year'=>'2013'],
        ];
	    // Jika menggunakan basis data maka:
        // $books = Book::find()->asArray()->all();
        return $books;
    }

    public function actionBook()
    {
        $model = new \yii\base\DynamicModel([
            'title', 'author', 'year'
        ]);
        $model->addRule(['title'], 'string');
        $model->addRule(['description'], 'string');
        $model->addRule(['year'], 'integer');

        return $this->render('book', [
            'model' => $model,
            'books' => $this->getBooks(),
        ]);

    }

    public function actionGetBook($id)
    {
        $books = $this->getBooks();
        $bookSelected = [];
        foreach($books as $book){
            if($book['id']==$id){
                $bookSelected = $book;
            }
        }
        // $bookSelected = Book::findOne($id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'book' => $bookSelected,
        ];
    }

    public function getProvinces()
    {
        return (new \yii\db\Query())
            ->select('*')
            ->from('province')
            ->orderBy(['name' => SORT_DESC])
            ->all(\yii::$app->db);
    }

    public function actionDepdrop()
{
    $model = new \yii\base\DynamicModel([
        'province_id', 'city_id',
    ]);
    $model->addRule(['province_id'], 'integer');
    $model->addRule(['city_id'], 'integer');

    return $this->render('depdrop', [
        'model' => $model,
        'provinces' => $this->getProvinces(),
    ]);
}

public function actionGetCities($province_id)
{
    $cities = (new \yii\db\Query())
        ->select('*')
        ->from('city')
        ->where([
            'province_id'=>$province_id,
        ])
        ->all(\yii::$app->db);
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return [
        'cities' => $cities,
    ];
}


}
