<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/backend/user", name="backend_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backend/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //recupere le type d'encryption via $user puis encrypte le mdp en clair en bcrypt
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());

            //set la nouvelle valeur sur mon objet a enregistrer
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('backend_user_index');
        }

        return $this->render('backend/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('backend/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //je recupere l'ancien mdp au cas ou mon utilisateur ne souhaiterai pas le changer
        // en effet , cela sera sinon setté a null lors du handle request à cause du type email qui set a vide le mot de passe par defaut
        $oldPassword = $user->getPassword();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request); //handle request met a jour lui meme + l'objet passé en parametre

        if ($form->isSubmitted() && $form->isValid()) {

            //si le formulaire retourne un mdp inchangé  = null
            if(is_null($user->getPassword())){
                // je recupere l'ancien mdp pour le reinjecter en DB
                $encodedPassword = $oldPassword;

            } else {

                //si changement de mdp , je repete la procedure d'encodage
                $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            }

            //dans tout les cas je set un mot de passe encodé ancien ou nouveau
            $user->setPassword($encodedPassword);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('backend/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_user_index');
    }
}
