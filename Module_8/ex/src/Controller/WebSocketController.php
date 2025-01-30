<?php
// src/Controller/WebSocketController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebSocketController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient)
    {}

    #[Route('/ws/message', name: 'ws_send_message', methods: ['POST'])]
    public function sendMessage(): JsonResponse
    {
        try {
            $this->httpClient->request('POST', 'ws://localhost:8081', [
                'body' => json_encode([
                    'type' => 'message',
                    'content' => 'Test message'
                ])
            ]);

            return new JsonResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}