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
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/user", name="user")
     */
    public function user()
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
            $encoded = $encoder->encodePassword($User, $password);

            $User->setPassword($encoded);
            $User->setRoles(['ROLE_USER']);

            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute("app_login");
        }

        return $this->render("User/CreatProfil.html.twig", [
            "form" => $UserForm->createView()
        ]);
    }

    /**
     * @Route("/user/modification/{id}", name="modif_user" , methods="GET|POST")
     * @param User $User
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function Modif(UserPasswordEncoderInterface $encoder,Request $request, User $User)
    {
        $UserForm = $this->createForm(UserType::class, $User);

        $UserForm->handleRequest($request);
        if (
            $UserForm->isSubmitted() && $UserForm->isValid()
        ) {

            $em = $this->getDoctrine()->getManager();


            $password = $User->getPassword();
            $encoded = $encoder->encodePassword($User, $password);

            $User->setPassword($encoded);

            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute("app_login");
        }

        return $this->render("User/ModifUser.html.twig", [
            "User" => $User,
            "form" => $UserForm->createView()
        ]);
    }
        /**
         * @Route("/user/modification/{id}", name="sup_user" , methods="DELETE")
         * @param User $User
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function supr(User $User, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isCsrfTokenValid('delete' . $User->getId(), $request->get('_token'))){
            $em->remove($User);
            $em->flush();
        };
        return $this->redirectToRoute("user");
    }

}
