<?php

namespace App\Controller\Admin;

use App\Service\ImportFromFaceBook;
use App\Service\FbGraph;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    //#[Route('/admin', name: 'app_admin')]
    //public function index(): Response
    //{
    //
    //    
    //    return $this->render('admin/index.html.twig', [
    //        'controller_name' => 'AdminController',
    //    ]);
    //}

    #[Route('/facebook-pull-by-publication/', name: 'admin_pull_by_publication_facebook')]
    public function pullByPublication(FbGraph $fbGraph, ImportFromFaceBook $ImportFromFaceBook): Response
    {
        $publications = $fbGraph->getAllPublication();
        
        $projects = [];
        foreach ($publications as $publication){
            $project = $ImportFromFaceBook->createProjectFromAlbum($publication);
            $projects[]= $project;
        }
        
        dump($projects);
        dump($publications);


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/facebook-pull-by-albums/', name: 'albums')]
    public function pullByAlbums(FbGraph $fbgraph, $images_Directory): Response
    {
 
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/', name: 'app_admin')]
    public function admin(): Response
    {
 
        return $this->render('admin/index.html.twig');
    }
}