<?php

namespace app\modules\orders_listing\controllers;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\models\GetOrdersFilter;
use app\modules\orders_listing\services\OrderService;
use Generator;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class OrderController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly OrderService $orderService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        $filter = GetOrdersFilter::fromRequest(Yii::$app->request->get());
        $result = null;
        if (!$filter->hasErrors()) {
            $result = $this->orderService->getOrdersByFilters(GetOrdersQuery::fromFilter($filter));
        }
        return $this->render('index', [
            'filter' => $filter,
            'result' => $result,
        ]);
    }

    public function actionExport(): Response
    {
        $filter = GetOrdersFilter::fromRequest(Yii::$app->request->get());
        if ($filter->hasErrors()) {
            throw new BadRequestHttpException(implode('; ', $filter->getErrorMessages()));
        }
        $query = GetOrdersQuery::fromFilter($filter);

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->setDownloadHeaders($this->orderService->getExportFileName(), 'text/csv; charset=UTF-8');
        $response->stream = fn (): Generator => $this->orderService->exportOrdersCsvStream($query);

        return $response;
    }
}
