<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\Publication;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PublicationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class PublicationController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction(Request $request) {

        // Recuperamos el usario actual
        $user = $this->getUser();

        // Cargamos el entity Manager
        $em = $this->getDoctrine()->getManager();

        // Ahora creamos el formulario
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);

        // Recuperamos el formulario si ha sido enviado
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                // Subir la imagen
                $file = $form["image"]->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif")  {
                        $file_name = $user->getId() . time() . "." . $ext;
                        $file->move("uploads/publications/images", $file_name);
                        $publication->setImage($file_name);
                    } else {
                        $publication->setImage(null);
                    } // Si es un tipo de image correcto
                } else {
                    $publication->setImage(null);
                } // el usuario ha agregado una imagen

                // Subir el documento
                $doc = $form["document"]->getData();
                if (!empty($doc) && $doc != null) {
                    $ext = $doc->guessExtension();
                    if ($ext == "pdf")  {
                        $file_name = $user->getId() . time() . "." . $ext;
                        $doc->move("uploads/publications/documents", $file_name);
                        $publication->setDocument($file_name);
                    } else {
                        $publication->setDocument(null);
                    } // Si es un tipo de documento correcto
                } else {
                    $publication->setDocument(null);
                } // el usuario ha agregado un documento

                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime("now"));
                $em->persist($publication);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "La publicación se ha creado correctamente";
                } else {
                    $status = "Error al añadir la publicación";
                }

            } else {
                $status = "La publicación no se ha creado, porque el formulario no es válido!!";
            } // form is valid

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("home_publications");
        } // form is submitted

        $publication = $this->getPublications($request);


        return $this->render("AppBundle:Publication:home.html.twig", array(
            "form" => $form->createView(),
            "pagination" => $publication
        ));
    }


    public function getPublications(Request $request) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $publications_repo = $em->getRepository("BackendBundle:Publication");
        $following_repo = $em->getRepository("BackendBundle:Following");

        /*
            select text from publications where user_id = 3
            or user_id In (select followed from following where user = 3);
        */

        $following = $following_repo->findBy(array("user" => $user));

        $following_array = array();
        foreach ($following as $follow) {
            $following_array[] = $follow->getFollowed();
        }

        $query = $publications_repo->createQueryBuilder("p")
                    ->where("p.user = (:user_id) OR p.user IN (:following)")
                    ->setParameter("user_id", $user->getId())
                    ->setParameter("following", $following_array)
                    ->orderBy("p.id", "DESC")
                    ->getQuery();


        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate($query,
            $request->query->getInt("page", 1), 5);
        return $pagination;
    }


    public function removePublicationAction(Request $request, $id = null) {
        $em = $this->getDoctrine()->getManager();
        $publications_repo = $em->getRepository("BackendBundle:Publication");
        $publication = $publications_repo->find($id);
        $user = $this->getUser();
        if ($user->getId() == $publication->getUser()->getId()) {

            $em->remove($publication);
            $flush = $em->flush();

            if ($flush == null) {
                $status = "La publicación se ha borrado correctamente!!";
            } else {
                $status = "La publicación no se ha borrado!!";
            }
        } else {
            $status = "La publicación no se ha borrado!!";
        }

        return new Response($status);

    }

}