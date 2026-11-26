<?php

namespace app\modules\orders_listing\controllers;

use app\modules\orders_listing\dto\GetOrdersQuery;
use app\modules\orders_listing\models\GetOrdersFilter;
use app\modules\orders_listing\services\OrderService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class OrderController extends Controller
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 100;

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
        $filter = new GetOrdersFilter();
        $filter->load(Yii::$app->request->get());
        if (!$filter->validate()) {
            throw new BadRequestHttpException(
                'Invalid filter parameters: ' . implode('; ', $filter->getFirstErrors())
            );
        }
        $queryDTO = GetOrdersQuery::fromFilter($filter);
        $page = max(1, (int) Yii::$app->request->get('page', self::DEFAULT_PAGE));
        $perPage = (int) Yii::$app->request->get('per-page', self::DEFAULT_PER_PAGE);
        $result = $this->orderService->getOrdersByFilters($queryDTO, $page, $perPage);
        $currentParams = [
            'status' => $filter->status,
            'search' => $filter->search,
            'search-type' => $filter->searchType,
            'serviceId' => $filter->serviceId,
            'mode' => $filter->mode,
        ];

        return $this->render('index', [
            'filter' => $filter,
            'dataProvider' => $result['dataProvider'],
            'serviceCounts' => $result['serviceCounts'],
            'services' => $result['services'],
            'currentParams' => $currentParams,
        ]);
    }

    public function actionExport(): Response
    {
        $filter = new GetOrdersFilter();
        $filter->load(Yii::$app->request->get());
        if (!$filter->validate()) {
            throw new BadRequestHttpException(
                'Invalid filter parameters: ' . implode('; ', $filter->getFirstErrors())
            );
        }
        $queryDTO = GetOrdersQuery::fromFilter($filter);
        $fileName = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        $file = fopen('php://temp', 'w+');
        if ($file === false) {
            throw new \RuntimeException('Cannot open temp stream');
        }
        // XXX: add mark for utf-8 to csv file
        fwrite($file, "\xEF\xBB\xBF");
        foreach ($this->orderService->exportOrdersCsv($queryDTO) as $row) {
            fputcsv($file, $row, ';');
        }
        rewind($file);
        $content = stream_get_contents($file);
        fclose($file);

        return Yii::$app->response->sendContentAsFile($content, $fileName, [
            'mimeType' => 'text/csv',
            'inline'   => false,
        ]);
    }
}
