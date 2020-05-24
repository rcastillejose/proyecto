<?php
namespace App\Controller;

use App\Entity\Group;
use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home_redireccion")
     */
    public function redireccionHome()
    {
        if($this->getUser()->getCliente() == 1){
            return $this->redirectToRoute('home_cliente',array('cliente' => $this->getUser()->getUsername()));
        }else{
            return $this->redirectToRoute('ticket_index_soporte', array('group'=> $this->getUser()->getGrupo()));
        }

    }

    // /**
    //  * @Route("/soporte", name="soporte")
    //  */
    // public function mostrarInicio()
    // {
    //     $grupos = $this->getDoctrine()->getRepository(Group::class)->findAll();
        
    //     return $this->render('home.html.twig', array('grupos' => $grupos));
       
    // }
  

}