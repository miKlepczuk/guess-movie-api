<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
  /**
   * @Route("/", name="main_page", methods={"GET"})
   */
  public function index(): Response
  {
    
    return $this->redirectToRoute('app.swagger_ui');
  }
}
