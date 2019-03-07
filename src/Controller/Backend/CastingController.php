<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/casting", name="backend_")
 */
class CastingController extends AbstractController
{
    /**
     * @Route("/", name="casting_index", methods={"GET"})
     */
    /*public function index(CastingRepository $castingRepository): Response
    {
        return $this->render('backend/casting/index.html.twig', [
            'castings' => $castingRepository->findAll(),
        ]);
    }*/

    /**
     * @Route("/movie/{id}", name="casting_indexbymovie", methods={"GET"})
     */
    public function indexByMovie(Movie $movie, CastingRepository $castingRepository): Response
    {   
        $castings = $castingRepository->findByMovieQueryBuilder($movie);
       
        return $this->render('backend/casting/index.html.twig', [
            'castings' => $castings,
            'movie' => $movie
        ]);
    }

    /**
     * @Route("/new/movie/{id}", name="casting_new", methods={"GET","POST"})
     */
    public function new(Request $request,Movie $movie): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($casting);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute(
                'backend_casting_indexbymovie',
                [ 'id' => $casting->getMovie()->getId()]
            );
        }

        return $this->render('backend/casting/new.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
            'movie' => $movie
        ]);
    }

    /**
     * @Route("/{id}", name="casting_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Casting $casting): Response
    {
        return $this->render('backend/casting/show.html.twig', [
            'casting' => $casting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="casting_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Casting $casting): Response
    {
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute(
                'backend_casting_indexbymovie',
                [ 'id' => $casting->getMovie()->getId()]
            );
        }

        return $this->render('backend/casting/edit.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Casting $casting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$casting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($casting);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute(
            'backend_casting_indexbymovie',
            [ 'id' => $casting->getMovie()->getId()]
        );
    }
}
