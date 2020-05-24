<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Chat;
use App\Entity\Group;
use App\Mercure\CookieGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// use App\Service\DefaultService;


/**
 * @IsGranted("ROLE_USER")
 */
final class GroupChatController extends AbstractController
{
    private $topic = 'http://chat.workdoo/message/' ; 
    private $inicio = false;

    /**
     * @Route("/chat", name="chats")
     */
    public function mostrarChats()
    {
        $grupos = $this->getDoctrine()->getRepository(Group::class)->findAll();
        
        return $this->render('chat/index.html.twig', array('grupos' => $grupos));
       
    }

    /**
     * @Route("/chat/{group}", name="chat")
     */
    public function __invoke(CookieGenerator $cookieGenerator, string $group): Response
    {
        $respuesta = "";
        $mensajes = null;

        $grupo = $this->getDoctrine()->getRepository(Group::class)->findOneBy([
            "name" => $group
        ]);


        if (!$this->inicio) {
            $chat = $this->getDoctrine()->getRepository(Group::class)->findOneBy([
                "name" => $group
            ]);

            $mensajes = $grupo->getChats();

            $this->inicio = true;
        }

        $respuesta = $this->render('chat/chat.html.twig', [
            "mensajes" => $mensajes,
            "grupo" => $grupo
        ]);
        $response=$respuesta;

        $response->headers->setCookie($cookieGenerator->generate());

        return $response;
    }


}