<?php

namespace App\Controller;

use App\Entity\Console;
use App\Form\ConsoleType;
use App\Service\PicturesManager;
use App\Repository\ConsoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function new(Request $request, ConsoleRepository $consoleRepository, PicturesManager $picturesManager): Response
    {
        $console = new Console();
        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $consolePhoto = $form->get('photo')->getData();

            //! Test en cours de redimentionnement
            // Définition de la largeur et de la hauteur maximale
            $width = 500;
            $height = 500;

            // Cacul des nouvelles dimensions
            list($width_orig, $height_orig) = getimagesize($consolePhoto);

            $ratio_orig = $width_orig/$height_orig;

            if ($width/$height > $ratio_orig) {
            $width = $height*$ratio_orig;
            } else {
            $height = $width/$ratio_orig;
            }
            
            // Redimensionnement
            $image_p = imagecreatetruecolor($width, $height);
            $image = imagecreatefromjpeg($consolePhoto);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            //! Fin test

            if ($consolePhoto) {
                if(!$picturesManager->add($console, 'photo', $image_p, 'photos_consoles_directory')){
                    // $this->addFlash('warning', 'Erreur durant le chargement de la photo');
                    return $this->redirectToRoute('app_game_index', [], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

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
    public function edit(Request $request, Console $console, ConsoleRepository $consoleRepository, PicturesManager $picturesManager): Response
    {
        $this->denyAccessUnlessGranted('CONSOLE_EDIT', $console);

        $form = $this->createForm(ConsoleType::class, $console);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $consolePhoto = $form->get('photo')->getData();

            if ($consolePhoto) {
                if(!$picturesManager->add($console, 'photo', $consolePhoto, 'photos_consoles_directory')){
                    // $this->addFlash('warning', 'Erreur durant le chargement de la photo');
                    return $this->redirectToRoute('app_game_index', [], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

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
    public function delete(Request $request, Console $console, ConsoleRepository $consoleRepository, PicturesManager $picturesManager): Response
    {
        $this->denyAccessUnlessGranted('CONSOLE_EDIT', $console);
        
        if ($this->isCsrfTokenValid('delete'.$console->getId(), $request->request->get('_token'))) {
            if($console->getPhoto() !== null){
                $picturesManager->delete($console, 'photo', 'photos_consoles_directory');
            }

            $consoleRepository->remove($console, true);
        }

        return $this->redirectToRoute('app_console_index', [], Response::HTTP_SEE_OTHER);
    }
}
