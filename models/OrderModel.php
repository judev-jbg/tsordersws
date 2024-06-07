<?php


class OrderModel
{
    private $db;
    private $response = [];

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getOrderById($id)
    {
        $query = "CALL toolstock_amz.uSp_getOrdersDetailUnshippedByOrderId(:id)";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrderById::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = [$result];
            } else {
                error_log('OrderModel::getOrderById::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrderById::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function getOrdersPending()
    {
        $query = "CALL toolstock_amz.uSp_getOrdersDetailUnshippedWithOutStock()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrdersPending::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrdersPending::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrdersPending::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function getOrdersPendingUntilToday()
    {
        $query = "CALL toolstock_amz.uSp_getOrdersDetailUnshippedExpireToday()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrdersPendingUntilToday::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrdersPendingUntilToday::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrdersPendingUntilToday::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function getOrderOutOfStock()
    {
        $query = "CALL toolstock_amz.uSp_getOrdersDetailUnshippedWithOutStock()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrderOutOfStock::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrderOutOfStock::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrderOutOfStock::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function getOrderOutOfStockUntilToday()
    {
        $query = "CALL toolstock_amz.uSp_getOrdersDetailUnshippedExpireToday()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrderOutOfStockUntilToday::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrderOutOfStockUntilToday::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrderOutOfStockUntilToday::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function getOrdersHistory()
    {
        $query = "CALL toolstock_amz.uSp_getHistoryShipment()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrdersHistory::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrdersHistory::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrdersHistory::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function shipmentsGeneratedByFileName($data)
    {
        $query = "CALL toolstock_amz.uSp_getShipmentsGeneratedByFileName(:filename)";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':filename', $data["filename"]);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::shipmentsGeneratedByFileName::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = [$result];
            } else {
                error_log('OrderModel::shipmentsGeneratedByFileName::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::shipmentsGeneratedByFileName::Error : ' . $e->getMessage());
            return null;
        }
    }
}
