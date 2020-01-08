<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /** @Route ("/{_locale}",
     *  name="home",
     *  requirements={ "_locale": "fr|en"},
     *  defaults={"_locale": "fr"})
     */
    public function helloAction()
    {
        return $this->render('base.html.twig');
    }


}