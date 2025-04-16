<?php

namespace App\Controller;

use App\Entity\Trip;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ManagerRegistry $doctrine): Response //function index admin
    {
        $user = $this->getUser();// Get the current user
        if (!$user) {
            throw $this->createAccessDeniedException('You are not authorized to access this resource.'); // Check if user is logged in
        }

        return $this->render('Admin/index.html.twig', [
        ]); // Render the admin index page
    } 

    #[Route('/trip/status-count', name: 'trip_status_count', options:['expose' => true])] //URL for the status count function
    public function statusCount(ManagerRegistry $doctrine): JsonResponse //function to get the status count and return it as JSON
    {
        $em = $doctrine->getManager(); // Get the entity manager for selecting the trip data from the database

        // Get the count of trips by status
        $tripRepository = $em->getRepository(Trip::class); //get all the trip data from the database

        $statusCounts = $tripRepository->createQueryBuilder('t') // Create a query builder for the Trip entity
            ->select('t.status, COUNT(t.id) AS count')
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();

        // Get the total count of trips
        $totalTrips = $tripRepository->count([]);

        return new JsonResponse([
            'statusCounts' => $statusCounts,
            'totalTrips' => $totalTrips
        ]);
    }
}