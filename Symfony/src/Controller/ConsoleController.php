<?php

namespace App\Controller;

use App\Entity\Console;
use App\Form\ConsoleType;
use App\Repository\ConsoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/console")
 */
class ConsoleController extends AbstractController
{
    /**
     * @Route("/", name="app_console_index", methods={"GET"})
     */
    public function index(ConsoleRepository $consoleRepository): Response
    {
        return $this->render('console/index.html.twig', [
            'consoles' => $consoleRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/new", name="app_console_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ConsoleRepository $consoleRepository): Response
    {
        $console = new Console();
        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $console->setUser($this->getUser());
            $consoleRepository->add($console, true);

            return $this->redirectToRoute('app_console_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('console/new.html.twig', [
            'console' => $console,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_console_show", methods={"GET"})
     */
    public function show(Console $console): Response
    {
        $this->denyAccessUnlessGranted('CONSOLE_VIEW', $console);

        return $this->render('console/show.html.twig', [
            'console' => $console,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_console_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Console $console, ConsoleRepository $consoleRepository): Response
    {
        $this->denyAccessUnlessGranted('CONSOLE_EDIT', $console);

        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $consoleRepository->add($console, true);

            return $this->redirectToRoute('app_console_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('console/edit.html.twig', [
            'console' => $console,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_console_delete", methods={"POST"})
     */
    public function delete(Request $request, Console $console, ConsoleRepository $consoleRepository): Response
    {
        $this->denyAccessUnlessGranted('CONSOLE_EDIT', $console);
        
        if ($this->isCsrfTokenValid('delete'.$console->getId(), $request->request->get('_token'))) {
            $consoleRepository->remove($console, true);
        }

        return $this->redirectToRoute('app_console_index', [], Response::HTTP_SEE_OTHER);
    }
}
