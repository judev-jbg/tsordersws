<?php

class OrderController
{

    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function getOrderById($id)
    {
        $orderById = $this->orderModel->getOrderById($id);
        if ($orderById != null) {
            header('HTTP/1.1 200 OK');
            echo $orderById;
        }
    }
    public function getOrdersPending()
    {
        $orderPending = $this->orderModel->getOrdersPending();
        if ($orderPending != null) {
            header('HTTP/1.1 200 OK');
            echo $orderPending;
        }
    }
    public function insertOrderToShipment($data)
    {
        header('HTTP/1.1 200 OK');
        if ($this->isExistOrder($data["idOrder"])) {
            if ($this->orderNotShipped($data["idOrder"])) {
                $insertOrder = $this->orderModel->insertOrderToShipment($data);
                if ($insertOrder != null) {
                    echo $insertOrder;
                }
            } else {
                echo [
                    "header" => ["status" => "ok", "insertedRows" => 0],
                    "message" => "El pedido ya fue enviado"
                ];
            }
        } else {
            echo [
                "header" => ["status" => "ok", "insertedRows" => 0],
                "message" => "El pedido no existe"
            ];
        }
    }
    public function getOrdersPendingUntilToday()
    {
        $orderPendingUntilToday = $this->orderModel->getOrdersPendingUntilToday();
        if ($orderPendingUntilToday != null) {
            header('HTTP/1.1 200 OK');
            echo $orderPendingUntilToday;
        }
    }
    public function getOrderOutOfStock()
    {
        $orderOutOfStock = $this->orderModel->getOrderOutOfStock();
        if ($orderOutOfStock != null) {
            header('HTTP/1.1 200 OK');
            echo $orderOutOfStock;
        }
    }
    public function getOrderOutOfStockUntilToday()
    {
        $OrderOutOfStockUntilToday = $this->orderModel->getOrderOutOfStockUntilToday();
        if ($OrderOutOfStockUntilToday != null) {
            header('HTTP/1.1 200 OK');
            echo $OrderOutOfStockUntilToday;
        }
    }
    public function getOrdersHistory()
    {
        $ordersHistory = $this->orderModel->getOrdersHistory();
        if ($ordersHistory != null) {
            header('HTTP/1.1 200 OK');
            echo $ordersHistory;
        }
    }
    public function shipmentsGeneratedByFileName($fileName)
    {
        $shipmentsGenerated = $this->orderModel->shipmentsGeneratedByFileName($fileName);
        if ($shipmentsGenerated != null) {
            header('HTTP/1.1 200 OK');
            echo $shipmentsGenerated;
        }
    }
    public function registerShipment($data)
    {
        //Validar el tipo de envio (ShipmentType = > [usingFile, usingWS])
    }

    public function getOrdersSelectedShipment()
    {
        $selectedShipment = $this->orderModel->getOrdersSelectedShipment();
        if ($selectedShipment != null) {
            header('HTTP/1.1 200 OK');
            echo $selectedShipment;
        }
    }

    private function sendShipmentWS($data)
    {
    }

    private function isExistOrder($idOrder)
    {
        $rsp = $this->orderModel->isExistOrder($idOrder);
        return $rsp;
    }
    private function orderNotShipped($idOrder)
    {
        $rsp = $this->orderModel->orderNotShipped($idOrder);
        return $rsp;
    }
}
