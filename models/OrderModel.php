<?php


class Order {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    public function getOrdersPending() {
        // consulta a la base de datos para obtener órdenes pendientes
    }

    public function getOrdersPendingUntilToday() {
        // consulta a la base de datos para obtener órdenes pendientes hasta hoy
    }

    public function getOrderOutOfStock() {
        // consulta a la base de datos para obtener órdenes fuera de stock
    }

    public function getOrderOutOfStockUntilToday() {
        // consulta a la base de datos para obtener órdenes fuera de stock hasta hoy
    }

    public function getOrdersHistory() {
        // consulta a la base de datos para obtener historial de órdenes
    }

    public function getOrdersReadyToShip() {
        // consulta a la base de datos para obtener órdenes listas para enviar
    }

    public function updateOrdersToShip() {
        // actualiza órdenes para enviar
    }

    public function generateShipmentFile() {
        // genera archivo de envío
    }

    public function registerShipmentWS() {
        // registra envío en la base de datos
    }

    public function getOrder($id) {
        // consulta a la base de datos para obtener una orden específica
    }
}
