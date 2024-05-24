<?php

class OrderController
{

    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function getOrderById($id)
    {
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
