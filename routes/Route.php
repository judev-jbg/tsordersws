
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

    }

    


}


// $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
// $method = $_SERVER['REQUEST_METHOD'];

// $handlers = [
//     '/' => [
//         'GET' => 'homeHandler',
//     ],
//     '/users' => [
//         'GET' => 'usersHandler',
//     ],
//     '/contact' => [
//         'GET' => 'contactHandler',
//     ],
// ];

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