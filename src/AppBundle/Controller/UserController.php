<?php

namespace AppBundle\Controller;

use AppBundle\Form\RegisterType;
use BackendBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    // Acción Login
    public function loginAction(Request $request) {

        // Recuperamos el posible error al hacer login y el último usuario al que le ocurrión el error.
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();

        // Creación de la vista del formulario de login
        return $this->render("AppBundle:User:login.html.twig", array(
            "last_username" => $lastUserName,
            "error" => $error
        ));
    }

    // Procesa el formulario de registro
    public function registerAction(Request $request) {

        // Crear el formulario en la página de registro
        $user = new User();
        $form = $this->createForm(registerType::class, $user);

        // Recepción del formulario (hace un mapping con los valores del formulario y el objeto $user)
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                // Cargamos el entity manager para poder usar la tabla User
                $em = $this->getDoctrine()->getManager();
                // $user_repo = $em->getRepository("BackendBundle:User");

                // Comprobamos si el usuario ya existe en la base de datos
                $query = $em->createQuery("SELECT u FROM BackendBundle:User u
                                          WHERE u.email = :email OR u.nick = :nick")
                            ->setParameter("email", $form->get("email")->getData())
                            ->setParameter("nick", $form->get("nick")->getData());
                $user_isset = $query->getResult();

                if (count($user_isset) == 0) {
                    // No existe el usuario, luego hay que crearlo
                    // Codificamos la constraseña, le asignamos el rol por defecto y lo guardamos en BBDD.
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null);
                    $em->persist($user);
                    $flush = $em->flush(); // Pasa los objetos que tenemos como persist a la base de datos

                    if ($flush == null) {
                        $status = "Te has registrado correctamente !!";
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
                    } else {
                        $status = "No te has registrado correctamente !!";
                    }
                } else {
                    // Ya existe el usuario
                    $status = "El usuario ya existe !!";
                } // Si el usuario no existe

            } else {
                $status = "No te has registrado correctamente !!";
            } // si el formulario es válido (valid)

            $this->session->getFlashBag()->add("status", $status);

        } // Si se ha recibido el formulario (submitted)

        // Creación de la vista del formulario de registro
        return $this->render("AppBundle:User:register.html.twig", array(
            "form" => $form->createView()
        ));
    }


    // Recibe una petición Ajax que intenta determinar si el nick de un usuario ya existe
    public function nickTestAction(Request $request) {
        // Recuperamos el parámetro post nick
        $nick = $request->get("nick");

        // Cargamos el entityManager para saber si el nick ya existe
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");
        $usser_isset = $user_repo->findOneBy(array("nick" => $nick));
        $result = "used";

        if (is_object($usser_isset) && count($usser_isset) >= 1) {
            $result = "used";
        } else {
            $result = "unused";
        }

        return new Response($result);
    }

}