<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType; // You need to create this form if not already done
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Missing use
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/Admin', name: 'admin_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findAll();

        return $this->render('Admin/index.html.twig', [
            'events' => $events,
        ]);
    }
}
