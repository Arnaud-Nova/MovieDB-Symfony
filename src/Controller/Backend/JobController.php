<?php

namespace App\Controller\Backend;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
 Pour rappel si je souhaite créer directement mon controller dans le bon dossier , merci Aurelie & florian <3 :
 
 php bin/console make:Controller

Choose a name for your controller class (e.g. TinyPizzaController):
> Backend\MovieController

created: src/Controller/Backend/MovieController.php
created: templates/backend/movie/index.html.twig

*/
/**
 * @Route("/backend/job", name="backend_")
 */
class JobController extends AbstractController
{
    /**
     * @Route("/", name="job_index", methods={"GET"})
     */
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('backend/job/index.html.twig', ['jobs' => $jobRepository->findAll()]);
    }

    /**
     * @Route("/new", name="job_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );
            
            return $this->redirectToRoute('backend_job_index');
        }

        return $this->render('backend/job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Job $job = null): Response
    {
        if (!$job) {
            throw $this->createNotFoundException('Métier introuvable');
        }

        return $this->render('backend/job/show.html.twig', ['job' => $job]);
    }

    /**
     * @Route("/{id}/edit", name="job_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Job $job): Response
    {

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_job_index', ['id' => $job->getId()]);
        }

        return $this->render('backend/job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_job_index');
    }
}
