<?php
// src/Controller/PostController.php
namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private function broadcastWebSocket(array $data): void
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (!$socket) {
                error_log("❌ Erreur création socket");
                return;
            }
    
            if (!socket_connect($socket, '127.0.0.1', 8081)) {
                error_log("❌ Erreur connexion socket");
                return;
            }
    
            // Handshake HTTP
            $key = base64_encode(random_bytes(16));
            $headers = "GET / HTTP/1.1\r\n";
            $headers .= "Host: localhost:8081\r\n";
            $headers .= "Upgrade: websocket\r\n";
            $headers .= "Connection: Upgrade\r\n";
            $headers .= "Sec-WebSocket-Key: " . $key . "\r\n";
            $headers .= "Sec-WebSocket-Version: 13\r\n";
            $headers .= "\r\n";
    
            socket_write($socket, $headers);
    
            // Lire la réponse du handshake
            $response = socket_read($socket, 2048);
    
            // Envoyer le message masqué
            $message = json_encode($data, JSON_UNESCAPED_UNICODE);
            $frame = $this->createWebSocketFrame($message);
            socket_write($socket, $frame);
    
            socket_close($socket);
            error_log("✅ Message envoyé: " . $message);
        } catch (\Exception $e) {
            error_log("❌ Erreur: " . $e->getMessage());
        }
    }
    
    private function createWebSocketFrame(string $message): string
    {
        $messageLength = strlen($message);
        
        // Générer une clé de masquage aléatoire
        $maskingKey = random_bytes(4);
        
        // Premier octet: FIN = 1, RSV1-3 = 0, Opcode = 0x1 (text)
        $frame = chr(0x81);
        
        // Second octet: MASK = 1, Payload len = ?
        if ($messageLength <= 125) {
            $frame .= chr(0x80 | $messageLength);
        } elseif ($messageLength <= 65535) {
            $frame .= chr(0x80 | 126) . pack('n', $messageLength);
        } else {
            $frame .= chr(0x80 | 127) . pack('J', $messageLength);
        }
        
        // Ajouter la clé de masquage
        $frame .= $maskingKey;
        
        // Masquer le message
        $maskedMessage = '';
        for ($i = 0; $i < $messageLength; $i++) {
            $maskedMessage .= $message[$i] ^ $maskingKey[$i % 4];
        }
        
        return $frame . $maskedMessage;
    }

    private function encodeWebSocketMessage(string $message): string
    {
        $length = strlen($message);
        $header = "";
        
        if ($length <= 125) {
            $header = pack('CC', 0x81, $length);
        } elseif ($length <= 65535) {
            $header = pack('CCn', 0x81, 126, $length);
        } else {
            $header = pack('CCNN', 0x81, 127, 0, $length);
        }
        
        return $header . $message;
    }

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les posts (même si l'utilisateur n'est pas connecté)
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['created' => 'DESC']);
        
        // Création du formulaire
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }

    #[Route('/post/new', name: 'post_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour créer un post.'], 403);
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // Vérifier si un post avec le même titre existe déjà
        $existingPost = $entityManager->getRepository(Post::class)->findOneBy(['title' => $post->getTitle()]);
        if ($existingPost) {
            return new JsonResponse(['error' => 'Un post avec ce titre existe déjà.'], 400);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $postData = [
                'type' => 'post.created',
                'post' => [
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'content' => $post->getContent(),
                    'created' => $post->getCreated()->format('d/m/Y H:i')
                ]
            ];

            // Envoyer la mise à jour via WebSocket
            $this->broadcastWebSocket($postData);

            return new JsonResponse([
                'message' => 'Post créé avec succès !',
                'post' => $postData['post']
            ]);
        }

        return new JsonResponse(['error' => 'Formulaire invalide.'], 400);
    }

    #[Route('/view/{id}', name: 'post_view', methods: ['GET'])]
    public function view(Post $post): JsonResponse
    {
        return new JsonResponse([
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'created' => $post->getCreated()->format('d/m/Y H:i')
        ]);
    }

    #[Route('/delete/{id}', name: 'post_delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour supprimer un post.'], 403);
        }

        $postId = $post->getId();
        
        $entityManager->remove($post);
        $entityManager->flush();

        // Envoyer la mise à jour via WebSocket
        $this->broadcastWebSocket([
            'type' => 'post.deleted',
            'postId' => $postId
        ]);

        return new JsonResponse(['message' => 'Post supprimé avec succès !']);
    }
}