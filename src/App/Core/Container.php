<?php

namespace App\Core;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\UserDAOInterface;
use App\Services\Impl\AuthServiceImpl;
use App\DAO\Impl\UserDAOImpl;

class Container
{
    private static $instance = null;
    private $services = [];

    private function __construct()
    {
        $this->registerServices();
    }

    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function registerServices()
    {
        // Register DAO implementations
        $this->services[UserDAOInterface::class] = function() {
            return new UserDAOImpl();
        };

        // Register Service implementations
        $this->services[AuthServiceInterface::class] = function() {
            $userDAO = $this->get(UserDAOInterface::class);
            return new AuthServiceImpl($userDAO);
        };
    }

    public function get(string $interface)
    {
        if (!isset($this->services[$interface])) {
            throw new \Exception("Service not found: $interface");
        }

        return $this->services[$interface]();
    }
}