
<?php

class Route
{

    private $apiKeys;
    private $orderController;


    public function __construct()
    {
        $this->apiKeys = API_KEY;
        $this->orderController = new OrderController();
    }

    public function handleRequest()
    {

        $apiKey = $_SERVER['HTTP_API_KEY'] ?? "";
        error_log('apikey: ' . $apiKey);

        if ($apiKey != $this->apiKeys) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['ERROR' => 'NO AUTORIZADO']);
            exit;
        }


        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathSegments = explode('/', trim($path, '/'));
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->validateResource(strtolower($pathSegments[1]))) {
            if ($this->validateHttpVerbs($method, strtolower($pathSegments[1]))) {

                switch (strtolower($pathSegments[1])) {
                    case 'order':
                        if (count($pathSegments) == 3 && $pathSegments[2] != "") {
                            $this->orderController->getOrderById($pathSegments[2]);
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El recurso requiere un parametro (str|int) en este verbo HTTP y no puede estar vacio']);
                        }

                        break;

                    case 'orderspending':
                        switch ($method) {
                            case 'GET':
                                if (count($pathSegments) == 2) {
                                    $this->orderController->getOrdersPending();
                                } elseif (count($pathSegments) == 3 && strtolower($pathSegments[2]) == "untiltoday") {
                                    $this->orderController->getOrdersPendingUntilToday($pathSegments[2]);
                                } elseif (count($pathSegments) == 3 && strtolower($pathSegments[2]) == "delayed") {
                                    $this->orderController->getOrdersPendingDelayed($pathSegments[2]);
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El recurso requiere un parametro (str) y no puede estar vacio']);
                                }
        
                                break;
                            case 'PATCH':
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data !== null && !empty($data) && count($data) > 0) {
                                    $this->orderController->updateOrderFlagStock($data);
                                }else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'Este recurso no admite parametros']);
                                }
                            }
                            break;

                    case 'ordersoutofstock':
                        switch ($method) {
                            case 'GET':
                                if (count($pathSegments) == 2) {
                                    $this->orderController->getOrderOutOfStock();
                                } elseif (count($pathSegments) == 3 && strtolower($pathSegments[2]) == "untiltoday") {
                                    $this->orderController->getOrderOutOfStockUntilToday($pathSegments[2]);
                                } elseif (count($pathSegments) == 3 && strtolower($pathSegments[2]) == "delayed") {
                                    $this->orderController->getOrderOutOfStockDelayed($pathSegments[2]);
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El recurso requiere un parametro (str) y no puede estar vacio']);
                                }
                                break;
                            
                            case 'PATCH':
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data !== null && !empty($data) && count($data) > 0) {
                                    $this->orderController->updateOrderFlagFake($data);
                                }else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'Este recurso no admite parametros']);
                                }
                                break;
                        }

                        break;
                    case 'ordershistory':
                        if (count($pathSegments) == 2) {
                            $this->orderController->getOrdersHistory();
                        } else if (count($pathSegments) == 3 && $pathSegments[2] != "") {
                            $this->orderController->shipmentsGeneratedByFileName($pathSegments[2]);
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El recurso requiere un parametro (str) y no puede estar vacio']);
                        }

                        break;


                    case 'ordersshipfake':
                        if (count($pathSegments) == 2) {
                            $this->orderController->getOrdersShipFake();
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El metodo de este recurso no admite parametros']);
                        }

                        break;

                    case 'ordersreadytoship':
                        switch ($method) {
                            case 'GET':
                                if (count($pathSegments) == 2) {
                                    $this->orderController->getOrdersSelectedShipment();
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El metodo de este recurso no admite parametros']);
                                }
                                break;

                            case 'POST':
                                if (count($pathSegments) == 2) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data !== null && !empty($data) && count($data) > 0) {
                                        if (count($this->missingKeysForShipments($data)) == 0) {
                                            $this->orderController->insertOrderToShipment($data);
                                        } else {
                                            header('HTTP/1.1 400 Bad Request');
                                            echo json_encode(['error' => 'Este recurso espera ' . implode(", ", $this->missingKeysForShipments($data))]);
                                        }
                                    } else {
                                        header('HTTP/1.1 400 Bad Request');
                                        echo json_encode(['error' => 'Este recurso espera contenido en el cuerpo de la solicitud']);
                                    }
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El recurso no admite parametros en este verbo HTTP']);
                                }
                                break;

                            case 'PATCH':
                                if (count($pathSegments) == 2) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data !== null && !empty($data) && count($data) > 0) {
                                        if ($this->missingKeysForUpdateShipments($data["columnName"]) && array_key_exists("columnValue", $data) && array_key_exists("idOrder", $data)) {
                                            $this->orderController->updateOrderToShipment($data);
                                        } else {
                                            header('HTTP/1.1 400 Bad Request');
                                            $columnIncorrect = ($this->missingKeysForUpdateShipments($data["columnName"])) ? "" : " El valor de columnName (" . $data["columnName"] . ") no es valido";
                                            echo json_encode(['error' => 'Este recurso espera columnName, columnValue y idOrder.' . $columnIncorrect]);
                                        }
                                    } else {
                                        header('HTTP/1.1 400 Bad Request');
                                        echo json_encode(['error' => 'Este recurso espera contenido en el cuerpo de la solicitud']);
                                    }
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El recurso no admite parametros en este verbo HTTP']);
                                }
                                break;

                            case 'DELETE':
                                if (count($pathSegments) == 2) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data !== null && !empty($data) && count($data) > 0) {
                                        if (array_key_exists("idOrder", $data) && array_key_exists("shipmentType", $data)) {
                                            $this->orderController->deleteOrderToShipment($data);
                                        } else {
                                            header('HTTP/1.1 400 Bad Request');
                                            echo json_encode(['error' => 'Este recurso espera idOrder y shipmentType']);
                                        }
                                    } else {
                                        header('HTTP/1.1 400 Bad Request');
                                        echo json_encode(['error' => 'Este recurso espera contenido en el cuerpo de la solicitud']);
                                    }
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'El recurso no admite parametros en este verbo HTTP']);
                                }
                                break;
                        }

                        break;
                    case 'registershipment':
                        if (count($pathSegments) == 2) {
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data !== null && !empty($data) && count($data) > 0) {
                                if (array_key_exists("shipmentType", $data)) {
                                    $this->orderController->registerShipment($data);
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'Este recurso espera shipmentType']);
                                }
                            } else {
                                header('HTTP/1.1 400 Bad Request');
                                echo json_encode(['error' => 'Este recurso espera contenido en el cuerpo']);
                            }
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'Este recurso no admite parametros']);
                        }

                        break;

                    default:
                        header('HTTP/1.1 404 Not Found');
                        echo json_encode(['error' => 'El recurso de destino no existe']);
                        break;
                }
            } else {
                error_log("Access-Control-Allow-Methods: " . implode(", ", ROUTES[$pathSegments[1]]));
                header('HTTP/1.1 405 Method Not Allowed');
                header("Access-Control-Allow-Methods: " . implode(", ", ROUTES[$pathSegments[1]]));
                echo json_encode(['error' => 'El recurso de destino no admite este metodo']);
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'El recurso de destino no existe']);
        }
    }

    private function validateResource($requestUri)
    {
        if (array_key_exists($requestUri, ROUTES)) {
            return true;
        }
        return false;
    }

    private function validateHttpVerbs($method, $requestUri)
    {
        if (in_array($method, ROUTES[$requestUri])) {
            return true;
        }
        return false;
    }

    private function missingKeysForUpdateShipments($field)
    {
        $fieldsToUpdate = ["servicio", "horario", "destinatario", "direccion", "pais", "cp", "poblacion", "telefono", "email", "departamento", "contacto", "observaciones", "bultos", "movil", "refC"];
        if (in_array($field, $fieldsToUpdate)) {
            return true;
        }
        return false;
    }

    private function missingKeysForShipments($dataBody)
    {
        $MandatoryParams = ["servicio", "horario", "destinatario", "direccion", "pais", "cp", "poblacion", "telefono", "email", "departamento", "contacto", "observaciones", "bultos", "movil", "refC", "idOrder", "process", "shipmentType"];
        $MissingParams = array_diff($MandatoryParams, array_keys($dataBody));
        return $MissingParams;
    }
}


?>