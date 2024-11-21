<?php

namespace App\Services;

use App\Interfaces\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FileLogger implements LoggerInterface
{
    protected $logger;

    public function __construct(string $idFiliale)
    {
        $logPath = storage_path("logs/filiali/{$idFiliale}.log");

        $this->logger = new Logger($idFiliale);
        $this->logger->pushHandler(new StreamHandler($logPath, Logger::toMonologLevel('info')));
    }

    public function info(string $message): void
    {
        $this->logger->info($message);
    }

    public function error(string $message): void
    {
        $this->logger->error($message);
    }
}
