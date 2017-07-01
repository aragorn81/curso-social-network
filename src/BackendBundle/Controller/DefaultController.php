<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");
        $user = $user_repo->find(1);
        $users = $user_repo->findAll();

        echo "Bienvenido ".$user->getName() . " " . $user->getSurname() . ", " . $user->getEmail();
        var_dump($users);
        die();

        return $this->render('BackendBundle:Default:index.html.twig');
    }
}
