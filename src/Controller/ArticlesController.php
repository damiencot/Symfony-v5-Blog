<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\AntiSpam;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("{_locale}/blog", requirements={"_locale": "fr|en"})
 * @IsGranted("ROLE_USER")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route ("/list/{page}",
     * name="list_article",
     * defaults ={"page":1},
     * methods={"GET"})
     */
    public function listAction($page, EntityManagerInterface $em)
    {


        /**/
        if($page<1){
            throw $this->createNotFoundException("Page non trouvé");
        }

        $nbPerPage = $this->getParameter("nbArticlesPerPage");

        $articles=$em->getRepository('App:Article')->findOnlyPublishedWithPaging($page, $nbPerPage);
        $nbTotalPages = intval(ceil(count($articles) / $nbPerPage));


        return $this->render('article/list.html.twig',['articles' => $articles, 'page' => $page, 'nbPage' => $nbTotalPages]);
    }

    /**
     * @Route ("/article/{id}",
     * name="view_article",
     * requirements={"id": "\d+"})
     */
    public function viewAction($id, EntityManagerInterface $em) {
        $article = $em->getRepository('App:Article')->findOneBy(array('id' => $id));

        if($article ==null)
            throw $this->createNotFoundException("Page non trouvé");
        // incrémenter le
        // nombre de consultations
        $article->setNbViews($article->getNbViews()+1);
        //envoie à la bdd
        $em->flush();
        return $this->render('article/view.html.twig',['article'=>$article] );

    }

    /** @Route ("/article/add",
     *  name="add_article",
     *  methods={"GET", "POST"})
     *  @IsGranted("ROLE_ADMIN")
     */
    public function addAction(EntityManagerInterface $em,Request $request, AntiSpam $antiSpam){
        $article = new Article();
        $article ->setNbViews(1);
        $form=$this->createForm(ArticleType::class, $article);
        $form->add('send',SubmitType::class, ['label'=>'Ajouter un article', 'attr' => [
            'class' => 'mt-3 btn-primary'
        ]]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if ($antiSpam->isSpam($article->getContent())) {
                $this->addFlash('danger',"Spam");
                return $this->render('article/add.html.twig', array('form'=>$form->createView()));
            }

            $em->persist($article);
            $em->flush();
            $this->addFlash('success',"Tout s'est bien passé");
            return $this->redirectToRoute('list_article');
        }
        return $this->render('article/add.html.twig', array('form'=>$form->createView()));
    }



    /**
     * @Route ("/article/edit/{id}",
     * name="edit_article",
     * requirements={"id":"\d+"},
     * methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction(EntityManagerInterface $em, Request $request, $id, AntiSpam $antiSpam){
        $article = $em->getRepository('App:Article')->find($id);
        $form=$this->createForm(ArticleType::class, $article);
        $form->add('send',SubmitType::class, ['label'=>'Modifier l\'article', 'attr' => [
            'class' => 'mt-3 btn-primary'
        ]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($antiSpam->isSpam($article->getContent())) {
                $this->addFlash('danger',"Spam");
                return $this->render('article/edit.html.twig', array('form'=>$form->createView()));
            }
            $article ->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('success', "Modification");
            return $this->redirectToRoute('view_article', ['id' => $id]);
        }
        return $this->render('article/edit.html.twig', ['id' => $id, 'form'=>$form->createView()]);
    }

    /**
     * @Route ("/article/delete/{id}",
     * name="delete_article",
     * requirements={"page":"\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(EntityManagerInterface $em, int $id): Response
    {
        $article = $em->getRepository('App:Article')->find($id);

        if(!$article){
            throw new NotFoundHttpException("L'article n'existe pas");
        }
        $em->remove($article);
        $em->flush();
        $this->addFlash('success',"Suppression faite");
        return $this->redirectToRoute('list_article');
    }


    public function showAction($id, EntityManagerInterface $em)
    {
        // Préciser l'entité souhaitée. Remarque : la classe Repository n'a pas besoin d'exister
        // find() attend l'ID permettant de charger l'entité
        $article = $em->getRepository('App:Article')->find($id);// Ou $em->getRepository('App\Entity\Vehicle')
        if (!$article) {
            throw
            $this->createNotFoundException("Véhicule non trouvé");
        }

        return new Response("<body>{$article->getId()} {$article->getPlate()}</body>");
    }




    public function recentArticlesAction(EntityManagerInterface $em){
        $articles = $em->getRepository('App:Article')->findBy(array('published'=>true), array('created_at' => 'DESC'),2);
        $categories = $em->getRepository('App:Category')->findAll();
        return $this->render('last_articles.html.twig',['articles' => $articles, 'categories' => $categories] );
    }

    /**
     * @Route ("/category/{id}", name="category")
     */
    public function categoryAction (int $id, EntityManagerInterface $em){
        if($id){
            $category = $em->getRepository('App:Category')->find($id);

            $articles = $em->getRepository('App:Article')->findOnlyPublishedByCategory($category);

            return $this->render('article/list.html.twig', ['articles' => $articles]);
        }else {
            throw new NotFoundHttpException("La page n'existe pas");
        }
    }



}