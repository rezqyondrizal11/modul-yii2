<?php

namespace app\controllers;
use app\models\UserSocialMedia;
use app\models\User;
use yii\helpers\Url; 
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Gallery;
use app\models\ContactForm;
use yii\web\UploadedFile;
use app\models\UploadForm;
use yii\httpclient\Client;
use yii\helpers\Json;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
    
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionHello($nama)
    {
        return "Hello Word!".$nama;
    }

    public function actionTampil()
    {
        return $this->render('hello',[
            'nama'=>'riski',
        ]);
    }

    public function actionKomentar()
    {
        $model = new \app\models\Komentar();
    
        // Jika form di-submit dengan method POST
        if(Yii::$app->request->post()){
            $model->load(Yii::$app->request->post());
            if($model->validate()){
                Yii::$app->session->setFlash('success','Terima kasih ');       
            }
            else{
                Yii::$app->session->setFlash('error','Maaf, salah!');   
            }
            return $this->render('hasil_komentar', [
                'model' => $model,
            ]); 
        }
        else{
            return $this->render('komentar', [
                'model' => $model,
            ]); 
        }
    }

    public function actionQuery()
    {
        $db = Yii::$app->db;
        $command = $db->createCommand ('SELECT * FROM employee');
        $employees = $command->queryAll();

        foreach ($employees as $employee){
            echo "<br>";
            echo $employee ['id']." ";
            echo $employee ['name']." ";
            echo "(".$employee['age'].")";
        }
    }

    public function actionSignup()
    {
        $model = new \app\models\SignupForm();
        $session = Yii::$app->session;
        $attributes = $session['attributes'];

        if ($model->load(Yii::$app->request->post())) {
        if ($user = $model->signup()) {
            if ($session->has('attributes')){
                // add data user_social_media
                $user_social_media = new UserSocialMedia([
                  'social_media' => $attributes['social_media'],
                  'id'=>(string)$attributes['id'],
                  'username'=>$attributes['username'],
                  'user_id'=>$user->id,
                ]);
                $user_social_media->save();
            }

            if (Yii::$app->getUser()->login($user)) {
                return $this->goHome();
                }
            }
        }
        if ($session->has('attributes')){
            // set form field with data from social media
            $model->username = $attributes['username'];
            $model->email = $attributes['email'];
        }
    
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    
        public function successCallback($client)
        {
            $attributes = $this->safeAttributes($client);
          

         // find data social media in basis data
            $user_social_media = UserSocialMedia::find()
                ->where([
                 'social_media' => $attributes['social_media'],
                'id'=>(string)$attributes['id'],
                'username'=>$attributes['username'],
                ])
                ->one();

                // if data found
                if($user_social_media){
                    // get user from relation
                    $user = $user_social_media->user;
                    // check user is active
                    if($user->status==User::STATUS_ACTIVE){
                    // do automatic login
                     Yii::$app->user->login($user);
                }
                else{
                Yii::$app->session->setFlash('error','Login gagal, status user tidak aktif');
                }
            }
            else{
                // if data not found
                // check if email social media exists in tabel user
                $user = User::find()
                    ->where([
                      'email' => $attributes['email']
                    ])
                    ->one();
                // if user found
                if($user){
                    // check user is active
                    if($user->status==User::STATUS_ACTIVE){
                        // add to table user social media
                        $user_social_media = new UserSocialMedia([
                          'social_media' => $attributes['social_media'],
                          'id'=>(string)$attributes['id'],
                          'username'=>$attributes['username'],
                          'user_id'=>$user->id,
                        ]);
                        $user_social_media->save();
        
                        // do automatic login
                        Yii::$app->user->login($user);
                    }
                    else{
                        Yii::$app->session->setFlash('error','Login gagal, status user tidak aktif');
                    }
                }
                else{
                    // check if social media not twitter
                    if($attributes['social_media']!='twitter'){
                        // do automatic signup
                        $password = Yii::$app->security->generateRandomString(6);
                        $user = new User([
                          'username' => $attributes['username'],
                          'email' => $attributes['email'],
                          'password' => $password,
                        ]);
                        $user->generateAuthKey();
                        $user->generatePasswordResetToken();
                        if($user->save()){
                            $user_social_media = new UserSocialMedia([
                              'social_media' => $attributes['social_media'],
                              'id'=>(string)$attributes['id'],
                              'username'=>$attributes['username'],
                              'user_id'=>$user->id,
                            ]);
                            $user_social_media->save();
                            // do automatic login
                            Yii::$app->user->login($user);
                        }
                        else{
                            Yii::$app->session->setFlash('error','Login gagal, galat saat registrasi');
                        }
                    }
                    else{
                        // save data attributes to session
                        $session = Yii::$app->session;
                        $session['attributes']=$attributes;
        
                        // redirect to signup, via property successUrl
                        $this->action->successUrl = Url::to(['signup']);
                    }
                }
            }
        }
        
        public function safeAttributes($client){
            // get user data from client
            $attributes = $client->getUserAttributes();
            
            // set default value
            $safe_attributes = [
                'social_media'=> '',        
                'id'=> '',        
                'username'=> '',        
                'name'=> '',        
                'email'=> '',        
            ];
        
            // get value from user attributes base on social media
            if ($client instanceof \yii\authclient\clients\Facebook) {
                $safe_attributes = [
                    'social_media'=> 'facebook',        
                    'id'=> $attributes['id'],        
                    'username'=> $attributes['email'],        
                    'name'=> $attributes['name'],        
                    'email'=> $attributes['email'],        
                ];
            }
         else{
             return $this->redirect('login');
         }
            
            return $safe_attributes;
        }
        
        public function actionUpload()
{
    $user_id = \Yii::$app->user->id;
    $model = \app\models\UserPhoto::find()->where([
        'user_id' => $user_id
    ])->one();

    if(!$model){
        $model = new \app\models\UserPhoto([
            'user_id' => $user_id
        ]);
    }

    if (\Yii::$app->request->post()) {
        $model->photo = \yii\web\UploadedFile::getInstance($model, 'photo');
        if($model->validate()){
            $saveTo = 'uploads/' . $model->photo->baseName . '.' . $model->photo->extension;
            if($model->photo->saveAs($saveTo)){

                $model->save(false);
                Yii::$app->session->setFlash('success','Foto berhasil diupload');
            }
        }
    }

    return $this->render('upload', [
        'model' => $model
    ]);

}
public function actionGallery()
{
    $model = new \app\models\Gallery();
    if (\Yii::$app->request->post()) {
        $model->imageFiles = \yii\web\UploadedFile::getInstances($model, 'imageFiles');
        if ($model->validate()) {
            foreach ($model->imageFiles as $file) {
                $saveTo = 'uploads/' . $file->baseName . '.' . $file->extension;
                if ($file->saveAs($saveTo)) {
                    $model2 = new \app\models\Gallery([
                        'imageFiles' => $file->baseName . '.' . $file->extension,
                    ]);
                    $model2->save(false);
                }
            }
            \Yii::$app->session->setFlash('success', 'Image berhasil di upload');
        }
    }
    $model3 = \app\models\Gallery::find()->all();

    return $this->render('gallery', [
        'model' => $model,
        'model3' => $model3,
    ]);

}

public function actionHighchart()
{
    return $this->render('highchart');
}

public function actionDynamicChart()
{
    $db = \Yii::$app->db;
    $years = $db->createCommand('
        SELECT DISTINCT(year) FROM survey_framework
        ORDER BY year ASC')
        ->queryColumn();

    $frameworks = $db->createCommand('
        SELECT * FROM framework
        ORDER BY id ASC')
        ->queryAll();
    $series = [];
    foreach($frameworks as $framework){
        $results = $db->createCommand('
            SELECT total FROM survey_framework
            WHERE framework_id='.$framework['id'].'
            ORDER BY year ASC')
            ->queryColumn();
        $data = array_map('intval', $results);
        $series[] = [
            'name' => $framework['name'],
            'data' => $data,
        ];
    }

    return $this->render('dynamic-chart',[
        'years' => $years,
        'series' => $series,
        ]);
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
