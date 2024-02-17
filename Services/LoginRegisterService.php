<?php

declare(strict_types=1);

namespace Services;

use Minichan\Database\Database;
use PHP\LoginRegister\Response;
use PHP\LoginRegister\LoginRegisterServiceInterface;

class LoginRegisterService implements LoginRegisterServiceInterface {
    private Response $res;
    private Database $db;

    public function __construct() {
        $this->res = new Response();
        $this->db = new Database([
            'type' => 'mysql',
            'host' => 'localhost',
            'database' => 'login_register',
            'username' => 'tester',
            'password' => 'testeR123()!'
        ]);
    }
    public function AddUser(\Minichan\Grpc\ContextInterface $ctx, \PHP\LoginRegister\Users $request): Response {
        $existingUser = $this->db->has('users', [
            'fname' => $request->getFname(), 
            'lname' => $request->getLname(),
            'email' => $request->getEmail(),
        ]);

        if ($existingUser) {
            throw new \Minichan\Exception\AlreadyExistsException('User already exist');
        }

        $userData = [
            'unique_id' => $request->getUniqueId(),
            'fname' => $request->getFname(), 
            'lname' => $request->getLname(),
            'email' => $request->getEmail(),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            'img' => $request->getImg(),
        ];

        if ($this->db->insert('users', $userData)) {
            $this->res->setSuccess(true);    
            return $this->res->setUsers($request);
        } else {
            $this->res->setSuccess(false);
            return $this->res->setErrorMessage('Adding user failed');
        }
    }
    public function LoginUser(\Minichan\Grpc\ContextInterface $ctx, \PHP\LoginRegister\Users $request): Response {
        $data = $this->db->query("SELECT * FROM users WHERE email = '{$request->getEmail()}'");

        foreach ($data as $user) {
            if (password_verify($request->getPassword(), $user['password'])) {

                $request->setEmail($user['email'])
                        ->setUniqueId($user['unique_id'])
                        ->setFname($user['fname'])
                        ->setLname($user['lname'])
                        ->setImg($user['img'])
                        ->setUserId($user['user_id']);

                $u = $this->res->setUsers($request);
                print_r(json_decode($u->serializeToJsonString(), true));
                $this->res->setSuccess(true);    
                return $u;
            } else {
                $this->res->setSuccess(false);
                $this->res->setErrorMessage('Login Failed.');
                throw new \Minichan\Exception\InvokeException("authentication failed");
            }
        }
        throw new \Minichan\Exception\InvokeException("authentication failed");
    }
}