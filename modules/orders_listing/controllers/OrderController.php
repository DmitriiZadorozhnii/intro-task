<?php

namespace app\modules\orders_listing\controllers;

use app\modules\orders_listing\enums\order\OrderModeEnum;
use app\modules\orders_listing\enums\order\OrderStatusEnum;
use app\modules\orders_listing\forms\GetOrdersListingForm;
use app\modules\orders_listing\services\DTOs\PaginationDTO;
use app\modules\orders_listing\services\OrderService;
use yii\data\Pagination;
use yii\web\Controller;

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
        $request = \Yii::$app->request;
        $filter = new GetOrdersListingForm();
        $filter->load($request->get());
        $entity = $filter->entity();
        if (!$filter->validate() || is_null($entity = $filter->entity())) {
            // TODO: add my exception (or throw $filter to view)
        }
        //dd($request->get('status'));
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('pageSize', 100);
        $result = $this->orderService->getOrdersByFilters($entity, $page, $perPage);
        /** @var PaginationDTO $paginationDTO */
        $paginationDTO = $result['pagination'];
        $statuses = OrderStatusEnum::getNames();
        $modes = OrderModeEnum::getNames();
        // TODO: передавать $pagination в сервис
        $pagination = new Pagination([
            'totalCount' => $paginationDTO->getTotalCount(),
            'pageSize' => $perPage,
            'params' => $request->queryParams,
        ]);
        return $this->render('index', [
            'orders' => $result['items'],
            'pagination' => $pagination, // TODO: может все таки использовать свой PaginationDTO
            'totalCount' => $paginationDTO->getTotalCount(),
            'statuses' => $statuses,
            'currentStatus' => $entity->getStatus(),
            'modes' => $modes,
            'currentMode' => $entity->getMode(),
            'currentService' => $entity->getServiceId(),
            'serviceCounts' => [], // TODO: не забыть добавить сюда сервисы с глобальными счетчиками
            'searchQuery' => 'fdsf',//$entity->getSearchQuery(),
            'searchType' => 'fdsfs',//$entity->getSearchType(),
        ]);
    }
}
