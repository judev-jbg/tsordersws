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
        if (!$this->isExistOrder($data["idOrder"])) {
            $this->returnNoContent("El pedido no existe");
            return;
        }

        if (!$this->orderNotShipped($data["idOrder"])) {
            $this->returnNoContent("El pedido ya fue enviado");
            return;
        }

        $insertOrder = $this->orderModel->insertOrderToShipment($data);

        if ($insertOrder === null) {
            http_response_code(500);
            return;
        }

        $header = json_decode($insertOrder, true)["header"];

        if (!array_key_exists("insertedRows", $header) || $header["insertedRows"] <= 0) {
            $this->returnNoContent("No se inserto el registro");
            return;
        }

        http_response_code(201);
        echo $insertOrder;
    }
    private function returnNoContent($message)
    {
        http_response_code(202);
        echo json_encode([
            "header" => ["status" => "ok", "insertedRows" => 0],
            "message" => $message
        ]);
    }
    public function updateOrderToShipment($data)
    {
        $orderToShipment = $this->orderModel->updateOrderToShipment($data["columnName"], $data["columnValue"], $data["idOrder"]);

        if ($orderToShipment === null) {
            http_response_code(500);
            return;
        }

        http_response_code(201);
        echo $orderToShipment;
    }
    public function deleteOrderToShipment($data)
    {

        if (!$this->isExistOrder($data["idOrder"])) {
            $this->returnNoContent("El pedido no existe");
            return;
        }

        $deleteOrder = $this->orderModel->deleteOrderToShipment($data);

        if ($deleteOrder === null) {
            http_response_code(500);
            return;
        }

        $header = json_decode($deleteOrder, true)["header"];

        if (!array_key_exists("deletedRows", $header) || $header["deletedRows"] <= 0) {
            $this->returnNoContent("No se elimino el registro");
            return;
        }

        http_response_code(200);
        echo $deleteOrder;
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
        if ($data["shipmentType"] == "usingFile") {
            $shipmentsUsingFile = $this->registerShipmentFile($data);
            if ($shipmentsUsingFile != null) {
                header('HTTP/1.1 200 OK');
                echo $shipmentsUsingFile;
            }
        } else {
            $shipmentsUsingWS = $this->registerShipmentWS($data);
            if ($shipmentsUsingWS == null) {
                http_response_code(500);
                return;
            }

            if (!array_key_exists("content", $shipmentsUsingWS["header"]) || $shipmentsUsingWS["header"]["content"] <= 0) {
                echo json_encode($shipmentsUsingWS);
                return;
            }

            $result = $this->requestShipmentWS($shipmentsUsingWS["payload"]);
            if (count($result) > 0) {
                header('HTTP/1.1 200 Ok');
                $response = [
                    "header" => ["status" => "ok", "content" => 1],
                    "payload" => $result
                ];
            } else {
                header('HTTP/1.1 202 Accepted');
                $response = [
                    "header" => ["status" => "ok", "content" => 0],
                    "payload" => []
                ];
            }
            // var_dump($response);
            echo json_encode($response);
        }
    }

    public function getOrdersSelectedShipment()
    {
        $selectedShipment = $this->orderModel->getOrdersSelectedShipment();
        if ($selectedShipment != null) {
            header('HTTP/1.1 200 OK');
            echo $selectedShipment;
        }
    }

    private function registerShipmentWS($data)
    {
        $shipmentsWS = $this->orderModel->registerShipmentWS($data);
        if ($shipmentsWS != null) {
            return $shipmentsWS;
        }
    }
    private function registerShipmentFile($data)
    {
        $shipmentsFile = $this->orderModel->registerShipmentFile($data);
        if ($shipmentsFile != null) {
            header('HTTP/1.1 200 OK');
            return $shipmentsFile;
        }
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
    private function requestShipmentWS($data)
    {
        require_once "./vendor/autoload.php";
        $dotenv = Dotenv\Dotenv::createMutable("./");
        $dotenv->load();

        $staticData = [

            "uidCliente" => $_ENV["UID_GLS"],
            "urlSaveShip" => $_ENV["SAVE_SHIP"],
            "portes" => $_ENV["PORTES"],
            "reem" => $_ENV["REEMBOLSO"],
            "nombreOrg" => $_ENV["NOMBRE_ORG"],
            "direccionOrg" => $_ENV["DIRECCION_ORG"],
            "poblacionOrg" => $_ENV["POBLACION_ORG"],
            "codPaisOrg" => $_ENV["PAIS_ORG"],
            "cpOrg" => $_ENV["CP_ORG"],

        ];

        $objResponse = [];
        foreach ($data as $envio) {
            $xml = $this->generateXML($staticData, $envio);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_URL, $staticData["urlSaveShip"]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml; charset=UTF-8']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

            $postResult = curl_exec($ch);

            curl_close($ch);

            $xml = simplexml_load_string($postResult, NULL, 0, "http://www.w3.org/2003/05/soap-envelope");

            $xml->registerXPathNamespace('asm', 'http://www.asmred.com/');

            $resultNode = $xml->xpath("//asm:GrabaServiciosResponse/asm:GrabaServiciosResult")[0];
            $EnvioNode = $resultNode->xpath("//Servicios/Envio")[0];
            $returnAtt = (string) $EnvioNode->xpath("./Resultado/@return")[0];

            // $yearPath = date("Y");
            // $monthPath = date("m");
            // $dayPath = date("d");

            // $dateStr = date("dmY");
            // $timeStr = date("His");

            // $pathEtiqueta = '../etiquetas/' . $yearPath . "/" . $monthPath . "/" . $dayPath . "/";

            if ($returnAtt == "0") {

                $codBarAtt = (string) $EnvioNode->xpath("./@codbarras")[0];
                $uidAtt = (string) $EnvioNode->xpath("./@uid")[0];
                $expAtt = (string) $EnvioNode->xpath("./@codexp")[0];
                $etiqueta = $EnvioNode->xpath("./Etiquetas/Etiqueta")[0];

                $response = [
                    "codResponseWS" => $returnAtt,
                    "responseWS" => "",
                    "messageWS" => "Envio insertado Ok",
                    "idOrder" => $envio["idOrder"],
                    "uidExp" => $uidAtt,
                    "codBar" => $codBarAtt,
                    "exp" => $expAtt,
                ];


                // $decodedEtiqueta = base64_decode($etiqueta);
                $chunkSize = 1024;
                $chunks = str_split($etiqueta, $chunkSize);
                foreach ($chunks as $chunk) {
                    // Puedes enviar cada trozo por separado en la respuesta de tu web service REST
                    // Por ejemplo, utilizando JSON
                    $labelChunk[] = array('chunk' => $chunk);
                }

                $response["decodedLabel"] = $labelChunk;


                // if ($decodedEtiqueta !== false) {

                //     $response["decodedLabel"] = 1;
                //     $response["decodedLabelMessage"] = "Decodificacion exitosa";

                //     //Si el directorio dinamico no existe
                //     if (!file_exists($pathEtiqueta)) {
                //         //Crear directotio
                //         $flagDir = mkdir($pathEtiqueta, 0777, true);

                //         if ($flagDir) { //Creacion de directorio exitosa
                //             $filename = $pathEtiqueta . 'GLS_' . $dateStr  . "_" . $timeStr . "_" . $codBarAtt . ".pdf";
                //             $response["customPathLabel"] = 1;
                //         } else { //Creacion de directorio fallida
                //             $filename = 'GLS_' . $dateStr  . "_" . $timeStr . "_" . $codBarAtt . ".pdf";
                //             $response["customPathLabel"] = 0;
                //         }
                //         //Si el directorio dinamico ya existe
                //     } else {
                //         $filename = $pathEtiqueta . 'GLS_' . $dateStr  . "_" . $timeStr . "_" . $codBarAtt . ".pdf";
                //         $response["customPathLabel"] = 1;
                //     }


                //     if (file_put_contents($filename, $decodedEtiqueta) !== false) {
                //         $response["saveLabel"] = 1;
                //         $response["label"] = $filename;
                //     } else {
                //         $response["saveLabel"] = 0;
                //         $response["label"] = "";
                //     }
                // } else {
                //     $response["decodedLabel"] = 0;
                //     $response["decodedLabelMessage"] = "Error al decodificar la etiqueta";
                // }

            } else {
                $error = (string) $EnvioNode->xpath("./Errores/Error")[0];

                $response = [
                    "codResponseWS" => $returnAtt,
                    "responseWS" => $error,
                    "messageWS" => ($this->getErrorWS($returnAtt) == "") ? $error : $this->getErrorWS($returnAtt),
                    "idOrder" => $envio["idOrder"],
                ];
            }
            array_push($objResponse, $response);
        }
        return $objResponse;
    }
    private function generateXML($staticData, $envio)
    {
        $hoy = date("d/m/Y");
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
        <soap12:Body>
        <GrabaServicios  xmlns="http://www.asmred.com/">
        <docIn>
           <Servicios uidcliente="' . $staticData["uidCliente"] . '" xmlns="http://www.asmred.com/">
           <Envio>
              <Fecha>' . $hoy . '</Fecha>
              <Servicio>' . $envio["servicio"] . '</Servicio>
              <Horario>' . $envio["horario"] . '</Horario>
              <Bultos>' . $envio["bultos"] . '</Bultos>
              <Peso>' . $envio["peso"] . '</Peso>
              <Portes>' . $staticData["portes"] . '</Portes>
              <Importes>
                 <Reembolso>' . $staticData["reem"] . '</Reembolso>
              </Importes>
              <Remite>
                 <Nombre>' . $staticData["nombreOrg"] . '</Nombre>
                 <Direccion>' . $staticData["direccionOrg"] . '</Direccion>
                 <Poblacion>' . $staticData["poblacionOrg"] . '</Poblacion>
                 <Pais>' . $staticData["codPaisOrg"] . '</Pais>
                 <CP>' . $staticData["cpOrg"] . '</CP>
              </Remite>
              <Destinatario>
                 <Nombre>' . $envio["destinatario"] . '</Nombre>
                 <Direccion>' . $envio["direccion"] . '</Direccion>
                 <Poblacion>' . $envio["poblacion"] . '</Poblacion>
                 <Pais>' . $envio["pais"] . '</Pais>
                 <CP>' . $envio["cp"] . '</CP>
                 <Telefono>' . $envio["telefono"] . '</Telefono>
                 <Movil>' . $envio["movil"] . '</Movil>
                 <Email>' . $envio["email"] . '</Email>
                 <Departamento>' . $envio["departamento"] . '</Departamento>
                 <Observaciones>' . $envio["observaciones"] . '</Observaciones>
              </Destinatario>
              <Referencias>
                 <Referencia tipo="C">' . $envio["refC"] . '</Referencia>
              </Referencias>
              <DevuelveAdicionales>
                 <Etiqueta tipo="PDF"/>
              </DevuelveAdicionales>
           </Envio>
           </Servicios>
           </docIn>
        </GrabaServicios>
        </soap12:Body>
        </soap12:Envelope>';
        return $xml;
    }
    private function getErrorWS($idErrorWS)
    {
        $errors = [
            "+38" => [
                "Error, Número de teléfono del destinatario no válido.",
                "Referencia de objeto no establecida a una instancia de un objeto.",
                "Error en el nivel de transporte al enviar la solicitud al servidor. (provider: Proveedor de TCP, error: 0 - Se ha forzado la interrupcion de una conexion existente por el host remoto.",
                "No se ha podido convertir un objeto del tipo 'System.Xml.XmlComment' al tipo 'System.Xml.XmlElement'"
            ],
            "36" => "Error, Código postal del destinatario, formato incorrecto.",
            "-1" => "Tiempo de espera expirado.  Ha transcurrido el tiempo de espera antes de finalizar la operación o el servidor no responde.",
            "-3" => "Error, El código de barras del envío ya existe.",
            "-33" => "Cp destino no existe o no es de esa plaza",
            "-48" => "Error, servicio EuroEstandar/EBP: el número de paquetes debe ser siempre 1 (<Bultos>).",
            "-49" => "Error, servicio EuroEstandar/EBP: el peso debe ser <= 31,5 kgs (<Peso>).",
            "-50" => "Error, servicio EuroEstandar/EBP: no puede haber RCS (copia sellada de retorno), <Pod>.",
            "-51" => "Error, servicio EuroEstandar/EBP: no puede haber SWAP (<Retorno>).",
            "-52" => "Error, servicio EuroEstandar/EBP: se ha informado de un país que no está incluido en el servicio (<Destinatario>.<Pais>).",
            "-53" => "Error, servicio EuroEstandar/EBP: la agencia no está autorizada a insertar el servicio EuroEstandar/EBP.",
            "-54" => "Error, servicio EuroEstandar/EBP: La dirección de correo del destinatario es obligatoria (<Destinatario>.<Correo>).",
            "-55" => [
                "Error, servicio EuroEstandar/EBP: Se requiere el teléfono móvil del destinatario (<Destinatario>.<Movil>).",
                "Este contrato de valija no existe/esta dado de baja",
                "Formato de codigo de barras no reconocido",
                "Fecha expedicion anterior a hoy",
                "Los bultos no pueden ser 0 ni negativos",
                "No estas autorizado a grabar envios de ese cliente",
                "Sin tienda ps y horario ps / punto ps inexistente",
                "El servicio / horario es incorrecto"
            ],
            "-57" => "Error, servicio EuroEstandar/EBP: se ha notificado un país no incluido en el servicio (<Destinatario>.<Pais>).",
            "-59" => "Error, No puedo Canalizar, código postal del destinatario erróneo.",
            "-70" => "Error, El número de pedido ya existe (<Referencia tipo='0'> o 10 primeros dígitos de la <Referencia tipo='C'> si no existe tipo='0') a esta fecha y código de cliente.",
            "-80" => "Envíos EuroBusiness. Falta un campo obligatorio.",
            "-81" => "Envíos EuroBusiness. Se transmite un formato erróneo en el campo.",
            "-82" => "Envíos EuroBusiness. Código postal incorrecto/código de país incorrecto. Error en el código postal o en su formato, y quizás, una mala combinación de ciudad y código postal.",
            "-83" => "Envíos EuroBusiness. Error interno de GLS. No hay ningún número de paquete libre disponible dentro del intervalo.",
            "-84" => "Envíos EuroBusiness. Error interno de GLS. Falta un parámetro en el fichero de configuración de la UNI-BOX.",
            "-85" => "Envíos EuroBusiness. No se puede realizar el enrutamiento.",
            "-86" => "Envíos EuroBusiness. Error interno de GLS. No se puede encontrar o abrir un archivo de plantilla necesario.",
            "-87" => "Envíos EuroBusiness. Error interno de GLS. Secuencia duplicada.",
            "-88" => "Envíos EuroBusiness. Otros errores.",
            "-89" => "Error, Servicio EBP: algunos datos son erróneos.",
            "-96" => "Error, servicio EBP: Error secuencial.",
            "-97" => "Error, servicio EuroEstandar/EBP: <Portes> no puede ser 'D', <Reembolso> no puede ser > 0.",
            "-99" => "Advertencia, los servicios web están temporalmente fuera de servicio.",
            "-103" => "Error, plaza solicita es null (alta).",
            "-104" => "Error, plaza origen es null (alta).",
            "-106" => "Error, CodCli es null (alta).",
            "-107" => "Error, CodCliRed es null (alta).",
            "-108" => "Error, Sender Name debe tener al menos tres caracteres.",
            "-109" => "Error, La dirección del remitente debe tener al menos tres caracteres.",
            "-110" => "Error, La ciudad del remitente debe tener al menos tres caracteres.",
            "-111" => "Error, Sender Zipcode debe tener al menos cuatro caracteres.",
            "-117" => "Error, los locales solo en la plaza de origen para la web.",
            "-118" => "Error, la referencia del cliente está duplicada.",
            "-119" => "Error, excepción, error no controlado.",
            "-128" => "Error, Nombre del destinatario debe tener al menos tres caracteres.",
            "-129" => "Error, la dirección del destinatario debe tener al menos tres caracteres.",
            "-130" => "Error, La Ciudad del Destinatario debe tener al menos tres caracteres.",
            "-131" => "Error, Consignee Zipcode debe tener al menos cuatro caracteres.",
            "-6565" => "Error, Volumen incorrecto, recuerde que la unidad es m3.",
        ];
        if (array_key_exists($idErrorWS, $errors)) {
            return $errors[$idErrorWS];
        } else {
            return "";
        }
    }
}
