<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    public function new(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $User = new User();
        $UserForm= $this->createForm(UserType::class, $User);

        $UserForm->handleRequest($request);
        if (
            $UserForm->isSubmitted() && $UserForm->isValid()
        ){

            $em = $this->getDoctrine()->getManager();
            $em->persist($User);
            $em->flush();

            $this->addFlash("success", "Merci pour votre commentaire");

            //plus tard, rediriger vers la page de détails de la question
            return $this->redirectToRoute("app_login");
        }

        return $this->render("User/CreatProfil.html.twig", [
            "form" => $UserForm->createView()
        ]);
    }
}
