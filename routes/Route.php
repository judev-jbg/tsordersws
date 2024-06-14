
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

        // $apiKey = $_SERVER['HTTP_API_KEY'] ?? "";

        // if ($apiKey != $this->apiKeys) {
        //     header('HTTP/1.1 401 Unauthorized');
        //     echo json_encode(['ERROR' => 'NO AUTORIZADO']);
        //     exit;
        // }


        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathSegments = explode('/', trim($path, '/'));
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->validateResource(strtolower($pathSegments[1]))) {
            if ($this->validateHttpVerbs($method, strtolower($pathSegments[1]))) {

                switch ($pathSegments[1]) {
                    case 'order':
                        if (count($pathSegments) == 3 && $pathSegments[2] != "") {
                            $this->orderController->getOrderById($pathSegments[2]);
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El recurso requiere un parametro (str|int) en este verbo HTTP y no puede estar vacio']);
                        }

                        break;

                    case 'orderspending':
                        if (count($pathSegments) == 2) {
                            $this->orderController->getOrdersPending();
                        } elseif (count($pathSegments) == 3 && strtolower($pathSegments[2]) == "untiltoday") {
                            $this->orderController->getOrdersPendingUntilToday($pathSegments[2]);
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El recurso requiere un parametro (str) y no puede estar vacio']);
                        }

                        break;

                    case 'orderoutofstock':
                        if (count($pathSegments) == 2) {
                            $this->orderController->getOrderOutOfStock();
                        } elseif (count($pathSegments) == 3 && $pathSegments[2] == "untilToday") {
                            $this->orderController->getOrderOutOfStockUntilToday($pathSegments[2]);
                        } else {
                            header('HTTP/1.1 400 Bad Request');
                            echo json_encode(['error' => 'El recurso requiere un parametro (str) y no puede estar vacio']);
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

                    case 'ordersReadyToShip':
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
                                    if (count($data) > 0) {
                                        if (count($this->missingKeysForShipments($data)) == 0) {
                                            $this->orderController->insertOrderToShipment($data);
                                        } else {
                                            header('HTTP/1.1 400 Bad Request');
                                            echo json_encode(['error' => 'Este recurso espera ' . implode($this->missingKeysForExistOrder($data))]);
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
                                # code...
                                break;

                            case 'DELETE':
                                # code...
                                break;
                        }

                        break;
                    case 'registerShipment':
                        if (count($pathSegments) == 2) {
                            $data = json_decode(file_get_contents('php://input'), true);
                            if (count($data) > 0) {
                                if (count($this->missingKeysForShipments($data)) == 0) {
                                    $this->orderController->registerShipment($data);
                                } else {
                                    header('HTTP/1.1 400 Bad Request');
                                    echo json_encode(['error' => 'Este recurso tambien espera [] en el cuerpo']);
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
                header('HTTP/1.1 405 Method Not Allowed');
                header("Access-Control-Allow-Methods: " . implode(ROUTES[$pathSegments[1]]));
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

    private function missingKeysForExistOrder($dataBody)
    {
        $MandatoryParams = ["idOrder", "value", "shipmentType"];
        $MissingParams = array_diff($MandatoryParams, array_keys($dataBody));

        return $MissingParams;
    }
    private function missingKeysForShipments($dataBody)
    {
        $MandatoryParams = ["servicio", "horario", "destinatario", "pais", "cp", "poblacion", "telefono", "email", "departamento", "contacto", "observaciones", "bultos", "peso", "movil", "refC", "idOrder", "exported", "engraved", "process", "shipmentType"];
        $MissingParams = array_diff($MandatoryParams, array_keys($dataBody));

        return $MissingParams;
    }
}


?>