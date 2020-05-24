<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmpleadoType;
use App\Form\RegistroEmpleadoType;
use App\Form\ClienteType;
use App\Form\RegistroType;
use App\Form\PasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        
    }

    /**
     * @Route("/empleados", name="empleados_index", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function indexEmpleados(UserRepository $userRepository): Response
    {
        return $this->render('user/empleados.html.twig', [
            'users' => $userRepository->findBy(array('cliente' => 0)),
        ]);
    }

    /**
     * @Route("/clientes", name="clientes_index", methods={"GET"})
     */
    public function indexClientes(UserRepository $userRepository): Response
    {
        return $this->render('user/clientes.html.twig', [
            'users' => $userRepository->findBy(array('cliente' => 1)),
        ]);
    }

    /**
     * @Route("/cliente/nuevo", name="cliente_new", methods={"GET","POST"})
     */
    public function newCliente(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistroType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setGrupo(null);
            $user->setAdmin(0);
            $user->setCliente(1);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('clientes_index');
        }

        return $this->render('user/newCliente.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/empleado/nuevo", name="empleado_new", methods={"GET","POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function newEmpleado(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistroEmpleadoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail(null);
            $user->setCliente(0);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('empleados_index');
        }

        return $this->render('user/newEmpleado.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

     /**
     * @Route("/{id}/cliente", name="cliente_edit", methods={"GET","POST"})
     */
    public function editCliente(Request $request, User $user): Response
    {
        $form = $this->createForm(ClienteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('clientes_index');
        }

        return $this->render('user/editClient.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/empleado", name="empleado_edit", methods={"GET","POST"})
     * @isGranted("ROLE_ADMIN")
     */
    public function editEmpleado(Request $request, User $user): Response
    {
        $form = $this->createForm(EmpleadoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('empleados_index');
        }

        return $this->render('user/editEmpleado.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/pass", name="cambiar_password", methods={"GET","POST"})
     */
    public function cambiarPassword(Request $request, UserRepository $userRepository, String $id, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(PasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(array('id' => $id));

            if($user->getCliente()==0 && $this->getUser()->getAdmin()==0){
                return $this->redirectToRoute('app_logout');
            }

            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            if ($user->getCliente() == 1){
                $message = (new \Swift_Message('Su contraseña ha sido cambiada'))
                ->setFrom('noreply@ieslavereda.es')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/cambioPassword.html.twig',
                        ['user' => $user,
                        'password' => $form->get('password')->getData()]
                    ),
                    'text/html'
                );
                $mailer->send($message);

                return $this->redirectToRoute('clientes_index');
            } else {
                return $this->redirectToRoute('empleados_index');
            }
            

            
    
        }

        return $this->render('user/password.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }



}
