<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ClientHomeController extends AbstractController
{

    /**
     * @Route("/cliente/{cliente}", name="home_cliente")
     * @IsGranted("ROLE_CLIENT")
     */
    public function indexCliente(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findBy(array('cliente' => $this->getUser()->getId()),array('fecha'=>'DESC'));
        

        return $this->render('cliente/index.html.twig', [
            'controller_name' => 'ClientHomeController',
            'tickets' => $tickets,
        ]);
    }

    
    /**
     * @Route("/cliente/tabla/{cliente}", name="cambio_vista")
     * @IsGranted("ROLE_CLIENT")
     */
    public function cambioVista(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findBy(array('cliente' => $this->getUser()->getId()),array('fecha'=>'DESC'));
        

        return $this->render('cliente/vistaTabla.html.twig', [
            'controller_name' => 'ClientHomeController',
            'tickets' => $tickets,
        ]);
    }
    
}
