<?php
// src/Command/WebSocketServerCommand.php
namespace App\Command;

use App\WebSocket\WebSocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebSocketServerCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('websocket:server')
            ->setDescription('Démarre le serveur WebSocket')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Démarrage du serveur WebSocket sur le port 8081...');
        
        try {
            $server = new WebSocketServer();
            $server->start(8081);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}