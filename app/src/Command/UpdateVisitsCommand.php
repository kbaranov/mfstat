<?php
declare(strict_types=1);

namespace App\Command;

use App\Api\Controller\VisitsController;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateVisitsCommand extends Command
{
    public function __construct(
        protected Client $redis,
        protected LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:update-visits')
            ->setDescription('Update visits by countries.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->redis->set(
                VisitsController::VISITS_CACHE_STORAGE_KEY,
                json_encode($this->redis->hgetall(VisitsController::VISITS_STORAGE_KEY))
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Error while getting data from Redis', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}