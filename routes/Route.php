
<?php

class Route{

    private $apiKeys;
    private $orderController;


    public function __construct() {
        $this->apiKeys = API_KEY;
        $this->orderController = new OrderController();
    }

    public function handleRequest(){

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathSegments = explode('/', trim($path, '/'));
        $method = $_SERVER['REQUEST_METHOD'];

        if($this->validateEndPoint($pathSegments[1])){
            if($this->validateEndPoint($method,$pathSegments[1])){



                
            }else{
                header('HTTP/1.1 405 Method Not Allowed');
                header("Access-Control-Allow-Methods: POST");
                echo json_encode(['error' => 'el recurso de destino no admite este método']);
            }

        }else{
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'El recurso de destino no existe']);
        }

    }

    private function validateEndPoint($requestUri){
        if (array_key_exists($requestUri, ROUTES)){
            return true;
        }
        return false;
    }

    private function validateHttpVerbs($method, $requestUri){
        if (array_key_exists($method, ROUTES[$requestUri])){
            return true;
        }
        return false;
    }

}


// if (array_key_exists($requestUri, $handlers)) {
//     if (array_key_exists($method, $handlers[$requestUri])) {
//         $handler = $handlers[$requestUri][$method];
//         if (function_exists($handler)) {
//             call_user_func($handler);
//         } else {
//             http_response_code(404);
//             echo "No se encuentra el manejador";
//         }
//     } else {
//         http_response_code(405);
//         echo "Método no permitido";
//     }
// } else {
//     http_response_code(404);
//     echo "No se encuentra la ruta";
// }

// function homeHandler()
// {
//     echo "Bienvenido a la página de inicio";
// }

// function usersHandler()
// {
//     echo "Lista de usuarios";
// }

// function contactHandler()
// {
//     echo "Contáctanos";
// }


// 

?>