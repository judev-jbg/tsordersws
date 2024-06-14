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
    public function insertOrderToShipment($data)
    {

        try {
            if ($data["shipmentType"] == "usingFile") {
                $query = "CALL toolstock_amz.uSp_updateMarkShipment(:value, :idOrder)";
            } else {
                $query = "CALL toolstock_amz.uSp_updateSelectedShipment(:value, :idOrder)";
            }

            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':value', $data["value"]);
            $stmt->bindParam(':idOrder', $data["idOrder"]);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                error_log('OrderModel::insertOrderToShipment::Success::Solicitud exitosa, registro(s) actualizado(s): ' . $stmt->rowCount());
                $stmt->closeCursor();

                $query = "CALL toolstock_amz.insertOrderToShipment(:servicio
                                                                    ,:horario
                                                                    ,:destinatario
                                                                    ,:direccion
                                                                    ,:pais
                                                                    ,:cp
                                                                    ,:poblacion
                                                                    ,:telefono
                                                                    ,:email
                                                                    ,:departamento
                                                                    ,:contacto
                                                                    ,:observaciones
                                                                    ,:bultos
                                                                    ,:peso
                                                                    ,:movil
                                                                    ,:refC
                                                                    ,:idOrder
                                                                    ,:exported
                                                                    ,:engraved
                                                                    ,:process)";
                $stmt->bindParam(':servicio', $data["servicio"]);
                $stmt->bindParam(':horario', $data["horario"]);
                $stmt->bindParam(':destinatario', $data["destinatario"]);
                $stmt->bindParam(':direccion', $data["direccion"]);
                $stmt->bindParam(':pais', $data["pais"]);
                $stmt->bindParam(':cp', $data["cp"]);
                $stmt->bindParam(':poblacion', $data["poblacion"]);
                $stmt->bindParam(':telefono', $data["telefono"]);
                $stmt->bindParam(':email', $data["email"]);
                $stmt->bindParam(':departamento', $data["departamento"]);
                $stmt->bindParam(':contacto', $data["contacto"]);
                $stmt->bindParam(':observaciones', $data["observaciones"]);
                $stmt->bindParam(':bultos', $data["bultos"]);
                $stmt->bindParam(':peso', $data["peso"]);
                $stmt->bindParam(':movil', $data["movil"]);
                $stmt->bindParam(':refC', $data["refC"]);
                $stmt->bindParam(':idOrder', $data["idOrder"]);
                $stmt->bindParam(':exported', $data["exported"]);
                $stmt->bindParam(':engraved', $data["engraved"]);
                $stmt->bindParam(':process', $data["process"]);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    error_log('OrderModel::insertOrderToShipment::Success::Solicitud exitosa, registro insertado');
                    $this->response["header"] = ["status" => "ok", "insertedRows" => $stmt->rowCount()];
                    $this->response["message"] = "Registro insertado";
                } else {
                    error_log('OrderModel::insertOrderToShipment::Success::Solicitud exitosa, el registro no se insertó');
                    $this->response["header"] = ["status" => "ok", "insertedRows" => 0];
                    $this->response["message"] = "El registro no se insertó";
                }
            } else {
                error_log('OrderModel::insertOrderToShipment::Success::Solicitud exitosa, pero NO se actualizó el registro ' . $data["idOrder"]);
                $this->response["header"] = ["status" => "ok", "updatedRows" => 0];
                $this->response["message"] = "No se pudo actualizar el registro";
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::insertOrderToShipment::Error : ' . $e->getMessage());
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
    public function shipmentsGeneratedByFileName($fileName)
    {
        $query = "CALL toolstock_amz.uSp_getShipmentsGeneratedByFileName(:filename)";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':filename', $fileName);
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
    public function isExistOrder($idOrder)
    {
        $query = "CALL toolstock_amz.uSp_updateMarkShipment(:idOrder)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':idOrder', $idOrder);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function orderNotShipped($idOrder)
    {
        $query = "CALL toolstock_amz.uSp_updateMarkShipment(:idOrder)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':idOrder', $idOrder);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function getOrdersSelectedShipment()
    {
        $query = "CALL toolstock_amz.uSp_getOrdersSelectedShipment()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::getOrdersSelectedShipment::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = $result;
            } else {
                error_log('OrderModel::getOrdersSelectedShipment::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::getOrdersSelectedShipment::Error : ' . $e->getMessage());
            return null;
        }
    }
}
