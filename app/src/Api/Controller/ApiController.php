<?php
declare(strict_types=1);

namespace App\Api\Controller;

use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    public function __construct(
        protected Client $redis,
        protected LoggerInterface $logger
    ) {}
}