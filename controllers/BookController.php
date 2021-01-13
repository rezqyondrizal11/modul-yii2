<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\BookSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
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
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
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
     * Deletes an existing Book model.
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
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionExportExcel()
{
    $searchModel = new bookSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
    // set template
    $template = Yii::getAlias('@app/views/book').'/_export.xlsx';
    $objPHPExcel = $objReader->load($template);
    $activeSheet = $objPHPExcel->getActiveSheet();
    // set orientasi & ukuran kertas
    $activeSheet->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
                                ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
    $baseRow=3;
    foreach($dataProvider->getModels() as $book){
        $activeSheet->setCellValue('A'.$baseRow, $baseRow-2)
                    ->setCellValue('B'.$baseRow, $book->tittle)
                    ->setCellValue('C'.$baseRow, $book->description);
        $baseRow++;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="_export.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
    $objWriter->save('php://output');
    exit;

}

public function actionExportPdf()
{
    $searchModel = new BookSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $html = $this->renderPartial('_export',['dataProvider'=>$dataProvider]);
    $mpdf=new \mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
    $mpdf->WriteHTML($html);
    $mpdf->Output();
    exit;
}


}
