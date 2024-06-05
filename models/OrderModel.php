<?php


class OrderModel
{
    private $db;

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
                return json_encode($result);
            } else {
                return json_encode(["Sin resultados" => "No se ha encontrado el pedido o expedicion"]);
            }
        } catch (PDOException $e) {
            error_log('OrderModel::getOrderById::Error : ' . $e->getMessage());
            return null;
        }
    }
}
