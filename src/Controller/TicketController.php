<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Estado;
use App\Entity\User;
use App\Entity\Group;
use App\Entity\HistoricoTicket;
use App\Form\TicketType;
use App\Form\TicketNewType;
use App\Form\TicketReabrirType;
use App\Repository\TicketRepository;
use App\Repository\HistoricoTicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/ticket")
 * @IsGranted("ROLE_USER") 
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/todos", name="ticket_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        //->findAll(),
        $historico=false;
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findByOpen(),
            'historico' => $historico,
        ]);
    }

    /**
     * @Route("/historico", name="tickets_historico", methods={"GET"})
     */
    public function ticketsHistorico(TicketRepository $ticketRepository): Response
    {
        if($this->getUser()->getCliente() == 0 ){
            $historico=true;
            $estado = $this->getDoctrine()->getRepository(Estado::class)->findOneBy(array("nombre" => "Cerrado"));
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findBy(array('estado' => $estado),array('fecha' => 'DESC')),
                'historico' => $historico,
            ]);
        }
    }

    /**
     * @Route("/soporte/{group}", name="ticket_index_soporte", methods={"GET"})
     */
    public function indexSoporte(TicketRepository $ticketRepository): Response
    {
        $historico=false;
        // 'tickets' => $ticketRepository->findBy(array('grupo' => $this->getUser()->getGrupo(), 'estado')),
        // 'grupo' => $grupo,
        $grupo = $this->getUser()->getGrupo();
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findByGroup($this->getUser()->getGrupo()->getId()),
            'grupo' => $grupo,
            'historico' => $historico,
            
        ]);
    }


    /**
     * @Route("/nuevo", name="ticket_client_new", methods={"GET","POST"})
     * @IsGranted("ROLE_CLIENT")
     */
    public function newByClient(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketNewType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $estado = $this->getDoctrine()->getRepository(Estado::class)->findOneBy(array("nombre" => "Esperando"));
            $ticket->setEstado($estado);
            $ticket->setCliente($this->getUser());
            $ticket->setFecha(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('home_cliente', array ("cliente" => $this->getUser()));
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/reabrir", name="reabrir", methods={"GET","POST"})
     * @IsGranted("ROLE_CLIENT")
     */
    public function reabrir(Request $request, Ticket $ticket,String $id, TicketRepository $ticketRepository): Response
    {
        if ($this->getUser()==$ticket->getCliente()){

            $form = $this->createForm(TicketReabrirType::class, $ticket);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $estado = $this->getDoctrine()->getRepository(Estado::class)->findOneBy(array("nombre" => "Abierto"));
                $ticket->setEstado($estado);
                $ticket->setFecha(new \DateTime());
                $ticket->setRespuesta(null);
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ticket);
                // $entityManager->persist($ticketAux);
                $entityManager->flush();

                return $this->redirectToRoute('home_cliente', array ("cliente" => $this->getUser()));
            
            }

            return $this->render('ticket/reabrir.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
            ]);
        }else{
            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/mostrar/{id}/{hist?}", name="ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket, HistoricoTicketRepository $historicoTicketRepository, String $hist=null): Response
    {
        //$historicos = $ticket->getHistoricoTickets();
        if ($this->getUser()==$ticket->getCliente() || $this->getUser()->getCliente()==0){
            $historicos = $historicoTicketRepository->findBy(array("ticket" => $ticket),array('fecha'=>'DESC'));
            if ($this->getUser()->getCliente() == 1 ){

                return $this->render('ticket/showCliente.html.twig', [
                    'ticket' => $ticket,
                    'historicos' => $historicos,
                    
                ]);
            } else{
            
                return $this->render('ticket/showSoporte.html.twig', [
                    'ticket' => $ticket,
                    'historicos' => $historicos,
                    'hist' => $hist,
                ]);
                }
        } else {
            return $this->redirectToRoute('app_logout');
        }
        
    }

    /**
     * @Route("/{id}/responder", name="ticket_responder", methods={"GET","POST"})
     */
    public function responder(Request $request, Ticket $ticket, \Swift_Mailer $mailer): Response
    {

        if ($this->getUser()->getCliente() == 0){
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $estado = $this->getDoctrine()->getRepository(Estado::class)->findOneBy(array("nombre" => "Cerrado"));
                $ticket->setEstado($estado);
                $ticket->setEmpleado($this->getUser());
    
                $ticketAux = new HistoricoTicket();
                // $aux = $this->getDoctrine()->getRepository(Ticket::class)->findOneBy(array("nombre" => "Esperando"));
                $ticketAux->setTicket($ticket);
                $ticketAux->setCliente($ticket->getCliente());
                $ticketAux->setEmpleado($ticket->getEmpleado());
                $ticketAux->setEstado($ticket->getEstado());
                $ticketAux->setGrupo($ticket->getGrupo());
                $ticketAux->setFecha($ticket->getFecha());
                $ticketAux->setAsunto($ticket->getAsunto());
                $ticketAux->setMensaje($ticket->getMensaje());
                $ticketAux->setRespuesta($ticket->getRespuesta());
    
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ticketAux);
                
                $this->getDoctrine()->getManager()->flush();
    
                $message = (new \Swift_Message('Hemos resuelto su duda'))
                ->setFrom('noreply@ieslavereda.es')
                ->setTo($ticket->getCliente()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/respuestaTicket.html.twig',
                        ['user' => $ticket->getCliente(),
                        'ticket' => $ticket]
                    ),
                    'text/html'
                );
                $mailer->send($message);
    
                return $this->redirectToRoute('ticket_index');
            }
    
            return $this->render('ticket/responder.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_logout');
        }
       
    }

    /**
     * @Route("/{id}/transferir/{grupo?}/{empleado?}", name="transferir", methods={"GET","POST"})
     */
    public function transfer(Request $request, Ticket $ticket, String $grupo = null, String $empleado = null): Response
    {
        
        if($empleado){
            $entityManager = $this->getDoctrine()->getManager();
            $auxEmpleado = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('username' => $empleado));
            $ticket->setEmpleado($auxEmpleado);
            $ticket->setGrupo($auxEmpleado->getGrupo());
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index_soporte', array('group'=> $this->getUser()->getGrupo()));
        }

        if($grupo){
            $auxGrupo = $this->getDoctrine()->getRepository(Group::class)->findOneBy(array('name' => $grupo));
            $empleados = $auxGrupo->getUsers();
        }else {
            $empleados = $this->getDoctrine()->getRepository(User::class)->findBy(array('cliente' => 0));
        }

        $grupos = $this->getDoctrine()->getRepository(Group::class)->findAll();
        

        return $this->render('ticket/transferir.html.twig', [
            'ticket' => $ticket,
            'grupos' => $grupos,
            'empleados' => $empleados
            // 'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/transferirGrupo/{grupo}", name="transferir_grupo", methods={"GET","POST"})
     */
    public function transferGrupo(Request $request, Ticket $ticket, String $grupo): Response
    {
        if($this->getUser()->getCliente() == 0 ){
            $entityManager = $this->getDoctrine()->getManager();
            $grupoAux = $this->getDoctrine()->getRepository(Group::class)->findOneBy(array('name' => $grupo));
            $ticket->setEmpleado(null);
            $ticket->setGrupo($grupoAux);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index_soporte', array('group'=> $this->getUser()->getGrupo()));
        }else {
            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/{id}/coger/{empleado}", name="adquirir", methods={"GET","POST"})
     */
    public function coger(Request $request, Ticket $ticket, String $empleado): Response
    {
        
            if($this->getUser()->getCliente() == 0 ){
                $entityManager = $this->getDoctrine()->getManager();
                $auxEmpleado = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('username' => $empleado));
                $ticket->setEmpleado($auxEmpleado);
                $estado = $this->getDoctrine()->getRepository(Estado::class)->findOneBy(array("nombre" => "Abierto"));
                $ticket->setEstado($estado);
                $entityManager->flush();
    
                return $this->redirectToRoute('ticket_index_soporte', array('group'=> $this->getUser()->getGrupo()));
            }else {
                return $this->redirectToRoute('app_logout');
            }
           
    
    }

    // /**
    //  * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, Ticket $ticket): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {            
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($ticket);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('ticket_index');
    // }
}
