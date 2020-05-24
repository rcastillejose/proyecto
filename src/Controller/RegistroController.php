<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistroType;
use App\Security\LoginAuthenticator;
use App\Repository\UserRepository;
use App\Form\ForgetPassType;
use App\Service\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistroController extends AbstractController
{

    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registro", name="registro", methods={"GET","POST"})
     */
    public function registro(Request $request, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $error = '';
        $form = $this->createForm(RegistroType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAdmin(0);
            $user->setCliente(1);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            try{
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $message = (new \Swift_Message('Registro realizado correctamente'))
                ->setFrom('noreply@ieslavereda.es')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'emails/registro.html.twig',
                        ['user' => $user]
                    ),
                    'text/html'
                );
                $mailer->send($message);


                return $this->redirectToRoute('app_login');
            } catch(\Exception $e){
                $error = 'El nombre de usuario ya existe, intentelo de nuevo';
                return $this->render('user/registro.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'error' => $error]);
            }
        }

        return $this->render('user/registro.html.twig', [
            'error' => $error,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/forget", name="forget_password", methods={"GET","POST"})
     */
    public function forgetPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer): Response
    {
        $error ="";
        $form = $this->createForm(ForgetPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(array('email' => $form->get('email')->getData()));
            if ($user){
                $passwordGenerator = new PasswordGenerator();
                $password="";
                $password = $passwordGenerator->password();

                //continuar con el email y mover todo donde no se necesite usuario
                $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        $password
                    )
                );
                $this->getDoctrine()->getManager()->flush();

                $message = (new \Swift_Message('Hemos recibido su solicitud'))
                ->setFrom('noreply@ieslavereda.es')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/cambioPassword.html.twig',
                        ['user' => $user,
                        'password' => $password]
                    ),
                    'text/html'
                );
                $mailer->send($message);

                return $this->redirectToRoute('app_login');
            } else{
                $error ="hola";
            }
            
    
        }

        return $this->render('user/emailpass.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
    
}
