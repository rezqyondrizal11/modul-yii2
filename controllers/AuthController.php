<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Auth;
/**
 * AuthController implements the CRUD actions for User model.
 */
class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $app = \Yii::$app;
    $moduleID = $app->id;
    $namespace = trim($app->controllerNamespace, '\\') . '\\';
    $routes = $this->getRoutes($app, $moduleID, $namespace, $id);
    foreach ($app->getModules() as $moduleID => $child) {
        if (($module = $app->getModule($moduleID)) !== null) {
            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $routes = array_merge($routes, $this->getRoutes($module, $moduleID, $namespace, $id));
        }
    }

    return $this->render('view', [
        'model' => $this->findModel($id),
        'routes' => $routes,
    ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function getRoutes($app, $moduleID, $namespace, $user_id)
{
    $routes = [];
    $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
    foreach (scandir($path) as $file) {
        if (strcmp(substr($file, -14), 'Controller.php') === 0) {
            $controllerID = \yii\helpers\Inflector::camel2id(substr(basename($file), 0, -14));
            $className = $namespace . \yii\helpers\Inflector::id2camel($controllerID) . 'Controller';
            $controller = Yii::createObject($className, [$controllerID, $app]);
            $controllerID = $controller->uniqueId;
            foreach ($controller->actions() as $actionID => $value) {
                $auth = \app\models\Auth::find()->where([
                    'module'=>$moduleID,
                    'controller'=>$controllerID,
                    'action'=>$actionID,
                    'user_id'=>$user_id,
                ])->count();
                $routes[] = [
                    'module'=>$moduleID,
                    'controller'=>$controllerID,
                    'action'=>$actionID,
                    'auth'=>$auth,
                ];
            }

            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $actionID = \yii\helpers\Inflector::camel2id(substr($name, 6));
                    $auth = \app\models\Auth::find()->where([
                        'module'=>$moduleID,
                        'controller'=>$controllerID,
                        'action'=>$actionID,
                        'user_id'=>$user_id,
                    ])->count();
                    $routes[] = [
                        'module'=>$moduleID,
                        'controller'=>$controllerID,
                        'action'=>$actionID,
                        'auth'=>$auth,
                    ];

                }
            }
        }
    }
    return $routes;
}

public function actionProcessAuth($module,$controller,$action,$user_id)
{
    $params = [
        'module'=>$module,
        'controller'=>$controller,
        'action'=>$action,
        'user_id'=>$user_id,
    ];
    $auth = Auth::find()->where($params)->count();

        if($auth==0){
            $model = new Auth($params);
            $model->save();
        }
        return "success inserted";

}


public function actionDeleteAuth($module,$controller,$action,$user_id)
{
    $params = [
        'module'=>$module,
        'controller'=>$controller,
        'action'=>$action,
        'user_id'=>$user_id,
    ];


    $auth = Auth::find()->where($params)->count();

        if($auth>0) {
            Yii::$app->db->
            createCommand()->delete('auth',[
                'module'=>$module,
        'controller'=>$controller,
        'action'=>$action,
        'user_id'=>$user_id,
            ])->execute();
        }
        return "success deleted";
    
}

}
