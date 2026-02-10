<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot_page')]
    public function index(): Response
    {
        return $this->render('chatbot/index.html.twig');
    }

    #[Route('/chatbot/ask', name: 'chatbot_ask', methods: ['POST'])]
    public function ask(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $message = '';
        if ($request->getContent()) {
            $payload = json_decode($request->getContent(), true);
            if (is_array($payload)) {
                $message = (string) ($payload['message'] ?? '');
            }
        }
        if ($message === '') {
            $message = (string) $request->request->get('message', '');
        }

        $reply = $chatbot->ask($message);

        return new JsonResponse(['reply' => $reply]);
    }
}
