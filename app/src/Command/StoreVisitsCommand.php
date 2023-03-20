<?php
declare(strict_types=1);

namespace App\Command;

use App\Api\Controller\VisitsController;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreVisitsCommand extends Command
{
    private const LOOP_ITEMS_COUNT = 10;

    public function __construct(
        protected Client $redis,
        protected LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:store-visits')
            ->setDescription('Store visits by countries.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            try {
                $this->redis->multi();
                for ($i = 0; $i < self::LOOP_ITEMS_COUNT; $i++) {
                    $this->redis->lpop(VisitsController::VISITS_QUEUE_STORAGE_KEY);
                }
                $items = $this->redis->exec();
            } catch (\Throwable $exception) {
                $this->logger->error('Error while getting data from Redis', [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                ]);
                continue;
            }

            if (empty($items)) {
                sleep(1);
                continue;
            }

            try {
                $visits = [];
                foreach ($items as $country) {
                    if (key_exists($country, $visits)) {
                        $visits[$country]++;
                    } else {
                        $visits[$country] = 1;
                    }
                }

                $this->redis->multi();
                foreach ($visits as $country => $quantity) {
                    $this->redis->hincrby(VisitsController::VISITS_STORAGE_KEY, $country, $quantity);
                }
                $this->redis->exec();
            } catch (\Throwable $exception) {
                $this->logger->error('Error while storing data to Redis', [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                ]);

                // Push back to the queue
                foreach ($items as $country) {
                    $this->redis->rpush(VisitsController::VISITS_QUEUE_STORAGE_KEY, [$country]);
                }
            }
        }
    }
}