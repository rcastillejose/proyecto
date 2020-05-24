<?php
// src/Controller/MessageController.php
namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use App\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
// use App\Service\DefaultService;
use App\Entity\Messages;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @IsGranted("ROLE_USER") 
 */
final class PublishController extends AbstractController
{
    private $topic2 = 'http://chat.workdoo/message/';

    /**
     * @Route("/message", name="sendMessage", methods={"POST"})
     */
    public function __invoke(MessageBusInterface $bus, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        $mensaje = $request->request->get('message');
        $time = $request->request->get('time');
        $grupo = $request->request->get('groupName');
        $topic = $this->topic2.$grupo;

        $update = new Update($topic, json_encode([
            'user' => $user->getUsername(),
            'mensaje' => $mensaje,
            'time' => $time
        ]));
        $bus->dispatch($update);

        $params = [
            "mensaje" => $mensaje,
            "grupo" => $grupo,
            // "id" => $user->getId(),
        ];

        $this->guardarMensaje($params);

        return $this->redirectToRoute('chat', [
            'group' => $grupo,
        ]);
    }

    private function guardarMensaje(array $params)
    {
        $mensaje = new Chat();
        
        $grupo = $this->getDoctrine()->getRepository(Group::class)->findOneBy(["name" => $params["grupo"]]);

        $mensaje->setMensaje($params["mensaje"]);
        $mensaje->setGrupo($grupo);
        $mensaje->setUser($this->getUser());
        $mensaje->setFecha(new \DateTime());


        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($mensaje);

        $entityManager->flush();
         
    }
}