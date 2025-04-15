<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Form\Driver1Type;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver/admin')]
final class DriverAdminController extends AbstractController
{
    #[Route(name: 'app_driver_admin_index', methods: ['GET'])]
    public function index(DriverRepository $driverRepository): Response
    {
        return $this->render('driver_admin/index.html.twig', [
            'drivers' => $driverRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_driver_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $driver = new Driver();
        $form = $this->createForm(Driver1Type::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($driver);
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver_admin/new.html.twig', [
            'driver' => $driver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_driver_admin_show', methods: ['GET'])]
    public function show(Driver $driver): Response
    {
        return $this->render('driver_admin/show.html.twig', [
            'driver' => $driver,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_driver_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Driver $driver, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Driver1Type::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver_admin/edit.html.twig', [
            'driver' => $driver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_driver_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Driver $driver, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$driver->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($driver);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_driver_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
