<?php

namespace App\Controller;

use App\Entity\transport;
use App\Form\transport1Type;
use App\Repository\transportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/transport/admin')]
final class transportAdminController extends AbstractController
{
    #[Route(name: 'app_transport_admin_index', methods: ['GET'])]
    public function index(transportRepository $transportRepository): Response
    {
        return $this->render('transport_admin/index.html.twig', [
            'transports' => $transportRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transport_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transport = new transport();
        $form = $this->createForm(transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transport);
            $entityManager->flush();

            return $this->redirectToRoute('app_transport_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport_admin/new.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_admin_show', methods: ['GET'])]
    public function show(transport $transport): Response
    {
        return $this->render('transport_admin/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transport_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, transport $transport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(transport1Type::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transport_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport_admin/edit.html.twig', [
            'transport' => $transport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transport_admin_delete', methods: ['POST'])]
    public function delete(Request $request, transport $transport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transport_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
