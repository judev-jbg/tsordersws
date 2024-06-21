<?php


class OrderModel
{
    private $db;
    private $response;

    public function __construct()
    {
        $this->db = new Database();
        $this->response = [];
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
                $query = "CALL toolstock_amz.uSp_insertSelectedshipment(:servicio
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
                                                                        ,:movil
                                                                        ,:refC
                                                                        ,:idOrder
                                                                        ,:process)";

                $stmt = $this->db->connect()->prepare($query);
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
                $stmt->bindParam(':movil', $data["movil"]);
                $stmt->bindParam(':refC', $data["refC"]);
                $stmt->bindParam(':idOrder', $data["idOrder"]);
                $stmt->bindParam(':process', $data["process"]);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    error_log('OrderModel::insertOrderToShipment::Success::Solicitud exitosa, registro(s) insertado(s): ' . $stmt->rowCount());
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
            error_log('OrderModel::insertOrderToShipment::Error: ' . $e->getMessage());
            $stmt->closeCursor();
            return null;
        }
    }
    public function updateOrderToShipment($columnName, $columnValue, $idOrder)
    {
        $query = "UPDATE toolstock_amz.selectedShipment SET $columnName = :columnValue WHERE idOrder = :idOrder AND fileGenerateName IS NULL";
        error_log($columnName . ' ' . $columnValue . ' ' . $idOrder);
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':columnValue', $columnValue);
            $stmt->bindParam(':idOrder', $idOrder);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                error_log('OrderModel::updateOrderToShipment::Success::Solicitud exitosa, registro actualizado');
                $this->response["header"] = ["status" => "ok", "updatedRows" => $stmt->rowCount()];
                $this->response["message"] = "Registro actualizado";
            } else {
                error_log('OrderModel::updateOrderToShipment::Success::Solicitud exitosa, el registro no se actualizó');
                $this->response["header"] = ["status" => "ok", "updatedRows" => 0];
                $this->response["message"] = "No se actualizó el registro";
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::updateOrderToShipment::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function deleteOrderToShipment($data)
    {
        try {
            $query = "DELETE FROM toolstock_amz.selectedshipment WHERE idOrder = :idOrder";
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':idOrder', $data["idOrder"]);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                error_log('OrderModel::deleteOrderToShipment::Success::Solicitud exitosa, registro(s) eliminado(s): ' . $stmt->rowCount());
                $this->response["header"] = ["status" => "ok", "deletedRows" => $stmt->rowCount()];
                $this->response["message"] = "Registro eliminado";
                $stmt->closeCursor();

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
                    error_log('OrderModel::deleteOrderToShipment::Success::Solicitud exitosa, registro(s) actualizado(s): ' . $stmt->rowCount());
                } else {
                    error_log('OrderModel::deleteOrderToShipment::Success::Solicitud exitosa, pero el registro no se actualizó');
                }
            } else {
                error_log('OrderModel::deleteOrderToShipment::Success::Solicitud exitosa, pero NO se elimino el registro ' . $data["idOrder"]);
                $this->response["header"] = ["status" => "ok", "deletedRows" => 0];
                $this->response["message"] = "No se pudo eliminar el registro";
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::deleteOrderToShipment::Error : ' . $e->getMessage());
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
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "CALL toolstock_amz.uSp_isExistOrder(:idOrder)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':idOrder', $idOrder);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            error_log('OrderModel::isExistOrder::Success::El pedido si existe');
            return true;
        }
        error_log('OrderModel::isExistOrder::Error::El pedido no existe');
        return false;
    }
    public function orderNotShipped($idOrder)
    {
        $query = "CALL toolstock_amz.uSp_isOrderNotShipped(:idOrder)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':idOrder', $idOrder);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            error_log('OrderModel::orderNotShipped::Success::El no ha sido enviado');
            return true;
        }
        error_log('OrderModel::orderNotShipped::Error::El pedido ya fue enviado');
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
    public function registerShipmentFile($data)
    {
        $query = "CALL toolstock_amz.uSp_getOrdersSelectedShipment()";
        try {
            $stmt = $this->db->connect()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::registerShipmentFile::Success::Solicitud exitosa');
                $this->response["header"] = ["status" => "ok", "content" => 1];
                $this->response["payload"] = [$result];
            } else {
                error_log('OrderModel::registerShipmentFile::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
            }
            return json_encode($this->response);
        } catch (PDOException $e) {
            error_log('OrderModel::registerShipmentFile::Error : ' . $e->getMessage());
            return null;
        }
    }
    public function registerShipmentWS($data)
    {
        $query = "CALL toolstock_amz.uSp_getShipmentsGeneratedByFileName(:filename)";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->bindParam(':filename', $fileName);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                error_log('OrderModel::registerShipmentWS::Success::Solicitud exitosa');
                return $result;
                // $this->response["header"] = ["status" => "ok", "content" => 1];
                // $this->response["payload"] = [$result];
            } else {
                error_log('OrderModel::registerShipmentWS::Success::Solicitud exitosa, sin datos para mostrar');
                $this->response["header"] = ["status" => "ok", "content" => 0];
                $this->response["payload"] = [];
                return $this->response;
            }
        } catch (PDOException $e) {
            error_log('OrderModel::registerShipmentWS::Error : ' . $e->getMessage());
            return null;
        }
    }
}
