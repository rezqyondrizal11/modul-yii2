<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use yii\helpers\Json;
use yii\web\Response;

class TestController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public $layout= 'login';

    public function actionLogin()
    {
        return $this->render('form-login');
    }

    public function actionBlog()
    {
        // select layouts
    $this->layout = 'blog';
    $this->layout = 'blog2';
    // render view blog
    return $this->render('blog');
    }

    public function actionGetProvince($id=0)
{
    $client = new Client();
    $addUrl=($id>0)?'id='.$id:'';
    $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('get')
                ->setUrl('http://api.rajaongkir.com/starter/province?'.$addUrl)
                ->addHeaders([
                    'key' => 'a9379685281c3e88b1fdb5bcaa060c1f',
                ])
                ->send();
              
    if ($response->isOk) {
        $content = Json::decode($response->content);
        // $content['rajaongkir']['query']
        // $content['rajaongkir']['status']
        $results = $content['rajaongkir']['results'];
        if ($id > 0) {
            if(count($results)>0) {
                echo $results['province_id'] . ' - ';
                echo $results['province'] . '<br>';
            }
            else{
                echo "blank";
            }
        } else {
            foreach ($results as $provinces) {
                echo $provinces['province_id']." - ".$provinces['province']."<br>";
                Yii::$app->db->createCommand()->insert('province', [
                    'id' => $provinces['province_id'],
                    'name_province' => $provinces['province']
                ])->execute();
            }
        }
    }
    else{
        $content =Json::decode($response->content);
        echo $content['rajaongkir']['status']['description'];
    }
   
}
public function actionGetCity($id=0, $province=0)
{
    $client = new Client();
    
    $addUrl=($id>0)?'id='.$id.'&':'';
    $addUrl.=($province>0)?'province='.$province:'';
    $response = $client->createRequest()
        ->setFormat(Client::FORMAT_JSON)
        ->setMethod('get')
        ->setUrl('http://api.rajaongkir.com/starter/city?'.$addUrl)
        ->addHeaders([
            'key' => 'a9379685281c3e88b1fdb5bcaa060c1f',
        ])
        ->send();

        Yii::$app->db->createCommand()->insert('city', [
            'id' => $cities['city_id'],
            'province_id' => $cities['province_id'],
            'name' => $cities['city_name'],
            'type' => strtolower($cities['type']),
            'postal_code' => $cities['postal_code']
        ])->execute(); 
    if ($response->isOk) {
        $content = Json::decode($response->content);
        // $content['rajaongkir']['query']
        // $content['rajaongkir']['status']
        $results = $content['rajaongkir']['results'];
        if($id>0){
            if(count($results)>0) {
                echo "<h1>".$results['province_id']." - ".$results['province']."</h1>";
                echo $results['city_id']." - ".$results['city_name']." - ".$results['type']." - ".$results['postal_code']."<br>";
            }
            else{
                echo 'blank';
            }
        }
        else{
            if(count($results)>0) {
                $last_province = 0;
                foreach ($results as $cities) {
                    if ($last_province != $cities['province_id']) {
                        echo "<h1>" . $cities['province_id'] . " - " . $cities['province'] . "</h1>";
                        $last_province = $cities['province_id'];

                        Yii::$app->db->createCommand()->insert('city', [
                            'id' => $cities['city_id'],
                            'province_id' => $cities['province_id'],
                            'name' => $cities['city_name'],
                            'type' => strtolower($cities['type']),
                            'postal_code' => $cities['postal_code']
                        ])->execute();
                    }
                    echo $cities['city_id'] . " - " . $cities['city_name'] . " - " . $cities['type'] . " - " . $cities['postal_code'] . "<br>";
                }
            }
            else{
                echo 'blank';
            }
        }
    }
    else{
        $content = Json::decode($response->content);
        echo $content['rajaongkir']['status']['description'];
    }
}

public function actionCekOngkir()
{
    $model = new \yii\base\DynamicModel([
        'origin', 'destination', 'weight', 'courier',
    ]);
    $model->addRule(['origin', 'destination', 'weight', 'courier',], 'required');
    $model->addRule(['weight'], 'integer');
    $model->addRule(['courier'], 'in', ['range' => ['jne','pos','tiki']]);
    $results = [];
    if ($model->load(Yii::$app->request->post())) {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('http://api.rajaongkir.com/starter/cost')
            ->addHeaders([
                'key' => 'a9379685281c3e88b1fdb5bcaa060c1f',
            ])
            ->setData([
                'origin' => $model->origin,
                'destination' => $model->destination,
                'weight' => $model->weight,
                'courier' => $model->courier,
            ])
            ->send();
        if ($response->isOk) {
            $results = json_decode($response->content);
        }
    }
    return $this->render('cek-ongkir',[
        'model'=>$model,
        'results'=>$results,
    ]);

}

}
