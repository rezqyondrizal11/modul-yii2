<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
    public function actionCheck()
    {
        $category = Category::find()->select('id')->orderBy('id DESC')->one();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['lastId'=>$category->id];
    
    }
    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);    
        }
        else{
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);    
        }
    }


    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        }
        else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFLash ('success','Data berhasil disimpan');
            if (Yii::$app->request->isAjax){
                $model = new Category();
                return $this->renderAjax('create',[
                    'model' => $model,
                ]);
            }
         else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 
        else{
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        
    }
    

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFLash ('success','Data berhasil disimpan');} 
            if (Yii::$app->request->isAjax){
                return $this->renderAjax('update',[
                    'model' => $model,
                ]);
            }
         
        else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        // return $this->redirect(['index']);
    }


    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionImport()
{
    $modelImport = new \yii\base\DynamicModel([
        'fileImport' => 'File Import',
    ]);
    $modelImport->addRule(['fileImport'], 'required');
    $modelImport->addRule(['fileImport'], 'file', ['extensions'=>'ods,xls,
    xlsx'],['maxSize'=>1024*1024]);


    if (Yii::$app->request->post()) {
        $modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport, 'fileImport');
        if ($modelImport->fileImport && $modelImport->validate()) {
            $inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName );
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            $baseRow = 3;
            while(!empty($sheetData[$baseRow]['A'])){
                $model = new Category();
                $model->name = (string)$sheetData[$baseRow]['A'];
                $model->save();
                $baseRow++;
            }
            Yii::$app->getSession()->setFlash('success', 'Success');
        }
        else{
            Yii::$app->getSession()->setFlash('error', 'Error');
        }
    }


    return $this->render('import',[
        'modelImport' => $modelImport,
    ]);
}

public function actionExportExcel()
{
    $searchModel = new CategorySearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
    // set template
    $template = Yii::getAlias('@app/views/category').'/_export.xlsx';

    $objPHPExcel = $objReader->load($template);
    $activeSheet = $objPHPExcel->getActiveSheet();
    // set orientasi & ukuran kertas
    $activeSheet->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
                                ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
    $baseRow=5;
    foreach($dataProvider->getModels() as $category){
        $activeSheet->setCellValue('A'.$baseRow, $category->name);
               
        $baseRow++;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="_export.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
    $objWriter->save('php://output');
    unset($objPHPExcel);
    exit;
}
}
