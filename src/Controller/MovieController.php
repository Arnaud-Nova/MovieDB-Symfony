<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Movie;

use App\Entity\Casting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CastingRepository;

class MovieController extends Controller
{
    /**
     * @Route("/", name="movie_index", methods={"GET","POST"})
     */
    public function index(Request $request)
    {   
         $repository = $this->getDoctrine()->getRepository(Movie::class);
         

         $searchTitle = $request->request->get('title');

         if($searchTitle){

            $movies = $repository->findByTitle($searchTitle);
 
         } else {
             //Query builder
            $movies = $repository->findAllQueryBuilderOrderedByName();
         }

         $lastMovies = $repository->lastRelease(10);

        return $this->render('movie/index.html.twig',[
            'movies' => $movies,
            'last_movies' => $lastMovies,
            'searchTitle' => $searchTitle
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show", methods={"GET"})
     */
    public function show(Movie $movie, CastingRepository $castingRepository)
    {   
        $castings = $castingRepository->findByMovieQueryBuilder($movie);

        return $this->render('movie/show.html.twig',[
            'movie' => $movie,
            'castings' => $castings
        ]);
    }
}
