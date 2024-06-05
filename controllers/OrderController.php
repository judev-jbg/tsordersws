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
    }
    public function getOrdersPendingUntilToday()
    {
    }
    public function getOrderOutOfStock()
    {
    }
    public function getOrderOutOfStockUntilToday()
    {
    }
    public function getOrdersHistory()
    {
    }
    public function generateOrdersFileFromFileName($data)
    {
    }
    public function registerShipment($data)
    {
        //Validar el tipo de envio (ShipmentType = > [usingFile, usingWS])
    }
    private function generateOrdersFile($data)
    {
    }
    private function sendShipmentWS($data)
    {
    }
}
