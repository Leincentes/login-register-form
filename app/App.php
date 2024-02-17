<?php
declare(strict_types=1);

use eftec\bladeone\BladeOne;
use PHP\LoginRegister\LoginRegisterServiceClient;
use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HttpServer;

class App
{
    public static function start()
    {
        $app = new self();
        $app->initialize();
        $app->run();
    }

    private function initialize()
    {
        session_start();
        define('BASE_PATH', dirname(__DIR__, 1));
        require_once BASE_PATH . '/vendor/autoload.php';
    }

    private function run()
    {
        $host = 'localhost';
        $port = 9501;
        $documentRoot = __DIR__;

        $httpServer = new HttpServer($host, $port);
        $httpServer->set([
            'document_root' => $documentRoot,
            'enable_static_handler' => true,
            'start_session_id' => 10,
        ]);

        $appInstance = $this;

        $httpServer->on('start', function () use ($host, $port) {
            echo "Server started at http://$host:$port\n";
        });

        $httpServer->on('request', function (Request $req, Response $res) use ($appInstance) {
            $appInstance->handleRequest($req, $res);
        });

        echo "Starting server at http://$host:$port\n";
        $httpServer->start();
    }

    private function handleRequest(Request $req, Response $res)
    {
        $router = new Router($req, $res);
        $router->route();
    }
}

class Router
{
    private $request;
    private $response;

    public $sessionId;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    
    public function route()
    {
        $views = BASE_PATH . '/app/views';
        $cache = BASE_PATH . '/app/cache';
        $blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

        $conn = (new Client('127.0.0.1', 9502, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

        $service = new LoginRegisterServiceClient($conn);

        switch ($this->request->server['request_uri']) {
            case '/':
            case '/register':
                $content = $blade->run('register');
                $this->response->end($content);
                break;
            case '/register/add':
                $targetDir = __DIR__ . '/img/';
                
                $data = [
                    'unique_id' => rand(time(), 100000000),
                    'fname' => $this->request->post['fname'],
                    'lname' => $this->request->post['lname'],
                    'email' => $this->request->post['email'],
                    'password' => $this->request->post['password'],
                    'image' => $this->request->files['image']['name'],
                    'status' => "Active Now",
                ];

                $targetFile = $targetDir . basename($this->request->files['image']['name']);
                move_uploaded_file($this->request->files['image']['tmp_name'], $targetFile);
        
                $user = new \PHP\LoginRegister\Users();
                $user->setUniqueId($data['unique_id'])
                    ->setFname($data['fname'])
                    ->setLname($data['lname'])
                    ->setEmail($data['email'])
                    ->setPassword($data['password'])
                    ->setImg($data['image']);
        
                $this->response->status(200);

                $result = $service->AddUser($user, $data);
                if($result->getSuccess()) {
                    $this->sessionId = uniqid();
            
                    $_SESSION[$this->sessionId] = json_decode($result->serializeToJsonString(), true);
                    $_SESSION[$data['email']] = $this->sessionId;
                } else {
                    $result->getErrorMessage();
                }
                break;
            case '/login':
                $content = $blade->run('login');
                $this->response->end($content);
                break;
            case '/login/user':
                $data = [
                    'email' => $this->request->post['email'],
                    'password' => $this->request->post['password'],
                ];
                
                $user = new \PHP\LoginRegister\Users();
                $user->setEmail($data['email'])
                    ->setPassword($data['password']);
            
                $this->response->status(200);
                $result = $service->LoginUser($user, $data);
            
                if ($result->getSuccess()) {
                    $this->sessionId = uniqid();
            
                    $_SESSION[$this->sessionId] = json_decode($result->serializeToJsonString(), true);
                    $_SESSION[$data['email']] = $this->sessionId;
                } else {
                    $result->getErrorMessage();
                }
                break;
            
            case '/home':
                foreach ($_SESSION as $sessionId => $sessionData) {
                    if (isset($sessionData['users'])) {
                        $userData = $sessionData['users'];
                        if ($_SESSION[$userData['email']] === $sessionId) {
                            $fname = $userData['fname'];
                            $lname = $userData['lname'];
                            $img = $userData['img'];
                            $uniqueId = $userData['uniqueId'];
            
                            $content = $blade->run('home', [
                                'fname' => $fname,
                                'lname' => $lname,
                                'img' => $img,
                                'uniqueId' => $uniqueId,
                            ]);
                            $this->response->end($content);
                        }
                    }
                }
                $this->response->header('Location', '/login');
                break;
            case '/logout':
                session_unset();
                session_destroy();
                $this->response->write('<script>window.location.href = "/login";</script>');
                $_SESSION = [];
                break;
        }
    }
}

App::start();