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
     * @Route("/user/follow/{id}", name="followUser")
     * @param User $User
     */
    public function follow(User $User)
    {
        $em = $this->getDoctrine()->getManager();

        $follow = $User->getFollowers();
        $follow ++;

        $id = $User->getId();

        $User->setFollowers($follow);
        $em->flush();

        return $this->redirectToRoute("user_view", ["id" => $id]);
    }

    /**
     * @Route("/user/{id}", name="user_view", requirements={"id" : "[0-9]+"})
     */
    public function userview($id)
    {
        $UserRepository = $this->getDoctrine()
            ->getRepository(User::class);

        $User = $UserRepository -> find($id);

        $id2 = $this->getUser()->getId();

        if ($id == $id2){
            return $this->redirectToRoute("user_view_none", ["id" => $id]);
        }

        return $this->render("user/User.html.twig", [
            "User" => $User
        ]);
    }

    /**
     * @Route("/Personal_user/{id}", name="user_view_none", requirements={"id" : "[0-9]+"})
     */
    public function userviewnone($id)
    {
        $UserRepository = $this->getDoctrine()
            ->getRepository(User::class);

        $User = $UserRepository -> find($id);

        $id2 = $this->getUser()->getId();

        if ($id != $id2){
            return $this->redirectToRoute("user_view", ["id" => $id]);
        }

        return $this->render("user/Personal _User.html.twig", [
            "User" => $User
        ]);
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
            $User->setFollowers(0);
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
            return $this->redirectToRoute("user");
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

    /**
     * @Route("/user/setfollow/{id}", name="setfollow_user")
     * @param User $User
     */
    public function setfollow($id, User $User)
    {
        $em = $this->getDoctrine()->getManager();
        $User2 = $this->getUser();

        $qb = $em->createQueryBuilder();
        $id2 = $this->getUser()->getId();
        $id = (int) $id;
        var_dump($id2);

        $qb->select('u.id')
            ->from(User::class , 'u')
            ->innerJoin('u.Follow', 'f')
            ->innerJoin('u.users', 'i')
            //->where('f.id = :id2')
            //->setParameter('id2', $id2)
            //->setParameter('id', $id)
        ;

        $rep = $qb->getQuery()->getResult();

        var_dump($rep);

        if(!empty($rep)){
            $User->addUser($User2);
            $follow = $User->getFollowers();
            $follow ++;
            $User->setFollowers($follow);
            $em->flush();
            return $this->redirectToRoute("user_view", ["id" => $id]);
        }else{
            $User->removeUser($User2);
            $em->flush();
            return $this->redirectToRoute("user_view", ["id" => $id]);
        }
    }

}
