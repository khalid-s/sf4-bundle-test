<?php
/**
 * Created by iKNSA.
 * User: Khalid Sookia <khalidsookia@gmail.com>
 * Date: 14/03/2019
 * Time: 01:28
 */


namespace Acme\BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function index()
    {
        return $this->render('@AcmeBlog/app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
