<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Utils\Slugger;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Utils\FileUploader;

/**
 * @Route("/backend/movie", name="backend_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('backend/movie/index.html.twig', ['movies' => $movieRepository->findAll()]);
    }

    /**
     * @Route("/new", name="movie_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugger $slugger, FileUploader $fileUploader): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sluggifiedTitle = $slugger->sluggify($movie->getTitle());
            $movie->setSlug($sluggifiedTitle);

            //avant l'enregistrement d'un film je dois recuperer l'objet fichier qui n'est pas une chaine de caractere
            $file = $movie->getPoster();

            if(!is_null($movie->getPoster())){

                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec la vrai extension du fichier d'origine
                $fileName = $fileUploader->renameAndMove($file);

                $movie->setPoster($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );
            
            return $this->redirectToRoute('backend_movie_index');
        }

        return $this->render('backend/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="movie_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Movie $movie = null): Response
    {
        if (!$movie) {
            throw $this->createNotFoundException('Film introuvable');
        }

        return $this->render('backend/movie/show.html.twig', ['movie' => $movie]);
    }

    /**
     * @Route("/{id}/edit", name="movie_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Movie $movie, Slugger $slugger, FileUploader $fileUploader): Response
    {

        $oldPoster = $movie->getPoster();

        if(!empty($oldPoster)) {
            $movie->setPoster(
                new File($this->getParameter('poster_directory').'/'.$oldPoster)
            );
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $sluggifiedTitle = $slugger->sluggify($movie->getTitle());
            $movie->setSlug($sluggifiedTitle);

            if(!is_null($movie->getPoster())){

                $file = $movie->getPoster();
                
                // fonction 2 en 1  : renommage + deplacement dans poster path
                $fileName = $fileUploader->renameAndMove($file);
               
                $movie->setPoster($fileName);

                // ici si je change de poster alors l'ancien ne fait plus de sens
                // unlink() me permet de supprimer mon fichier 
                if(!empty($oldPoster)){

                    unlink(
                        $this->getParameter('poster_directory') .'/'.$oldPoster
                    );
                }

            } else {
                
                $movie->setPoster($oldPoster);//ancien nom de fichier
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_movie_index', ['id' => $movie->getId()]);
        }

        return $this->render('backend/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="movie_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($movie);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_movie_index');
    }

    /**
     * @return string
     */
    /*private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }*/
}
