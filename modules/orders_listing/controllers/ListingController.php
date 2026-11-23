<?php

namespace app\modules\orders_listing\controllers;

use app\modules\orders_listing\forms\GetOrdersListingForm;
use app\modules\orders_listing\services\OrderService;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ListingController extends Controller
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
        $filter = new GetOrdersListingForm();
        $filter->load(\Yii::$app->request->get());
        if (!$filter->validate() || is_null($entity = $filter->entity())) {
            // TODO: add my exception (or throw $filter to view)
            throw new NotFoundHttpException();
        }
        $orders = $this->orderService->getOrdersByFilters($entity);

        return $this->render('index');
    }
}
