<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use phpDocumentor\Reflection\Types\This;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index()
    {
        $ArticleReprository = $this->getDoctrine()
            ->getRepository(Article::class);

        $Articles = $ArticleReprository-> findAll();

        return $this->render('article/index.html.twig', [
            "Articles" => $Articles,
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_view", requirements={"id" : "[0-9]+"})
     */
    public function article($id)
    {
        $ArticleRepository = $this->getDoctrine()
            ->getRepository(Article::class);

        $Article = $ArticleRepository -> find($id);

        return $this->render("Article/Article.html.twig", [
            "Article" => $Article
        ]);
    }

    /**
     * @Route("/article/create", name="create_article")
     */
    public function new(Request $request)
    {
        $Article = new Article();
        $ArticleForm= $this->createForm(ArticleType::class, $Article);

        $ArticleForm->handleRequest($request);
        if (
            $ArticleForm->isSubmitted() && $ArticleForm->isValid()
        ){

            $em = $this->getDoctrine()->getManager();

            $Article->setDateCre(new \DateTime());
            $Article->setDateMaj(new \DateTime());
            $Article->setFollow(0);

            $user = $this->getUser();

            $Article->setUser($user);

            $em->persist($Article);
            $em->flush();
            return $this->redirectToRoute("article");
        }

        return $this->render("Article/CreatArticle.html.twig", [
            "Articleform" => $ArticleForm->createView()
        ]);
    }

    /**
     * @Route("/article/modification/{id}", name="modif_article" , methods="GET|POST")
     * @param Article $Article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modif(Article $Article, Request $request)
    {
        $ArticleForm= $this->createForm(ArticleType::class, $Article);

        $ArticleForm->handleRequest($request);
        if (
            $ArticleForm->isSubmitted() && $ArticleForm->isValid()
        ){

            $em = $this->getDoctrine()->getManager();

            $Article->setDateMaj(new \DateTime());

            $em->persist($Article);
            $em->flush();
            return $this->redirectToRoute("article");
        }

        return $this->render("Article/CreatArticle.html.twig", [
            "article" => $Article,
            "Articleform" => $ArticleForm->createView()
        ]);
    }
    /**
     * @Route("/article/modification/{id}", name="sup_article" , methods="DELETE")
     * @param Article $Article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function supr(Article $Article, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isCsrfTokenValid('delete' . $Article->getId(), $request->get('_token'))){
            $em->remove($Article);
            $em->flush();
        };
        return $this->redirectToRoute("article");
    }

    /**
     * @Route("/article/follow/{id}", name="follow")
     * @param Article $Article
     */
    public function follow(Article $Article)
    {
        $em = $this->getDoctrine()->getManager();

        $follow = $Article->getFollow();
        $follow ++;

        $id = $Article->getId();

        $Article->setFollow($follow);
        $em->flush();

        return $this->redirectToRoute("article_view", ["id" => $id]);
    }
}
