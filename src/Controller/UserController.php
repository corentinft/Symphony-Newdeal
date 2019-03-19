<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $UserReprository = $this->getDoctrine()
            ->getRepository(User::class);

         $Users = $UserReprository -> findAll();

        return $this->render('user/index.html.twig', [
            "Users" => $Users,
        ]);
    }

    /**
     * @Route("/create", name="create_profil")
     */
    public function new(UserPasswordEncoderInterface $encoder,Request $request)
    {
        $User = new User();
        $UserForm= $this->createForm(UserType::class, $User);

        $UserForm->handleRequest($request);
        if (
            $UserForm->isSubmitted() && $UserForm->isValid()
        ){

            $em = $this->getDoctrine()->getManager();


            $password = $User->getPassword();
            var_dump($password);
            $encoded = $encoder->encodePassword($User, $password);

            var_dump($encoded);
            $User->setPassword($encoded);

            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute("app_login");
        }

        return $this->render("User/CreatProfil.html.twig", [
            "form" => $UserForm->createView()
        ]);
    }
}
