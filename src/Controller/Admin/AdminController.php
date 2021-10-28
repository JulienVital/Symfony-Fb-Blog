<?php

namespace App\Controller\Admin;

use App\Service\ImportFromFaceBook;
use App\Service\FbGraph;
use App\Service\FbGraph\GetAllPublications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    #[Route('/facebook-pull-by-publication/', name: 'admin_pull_by_publication_facebook')]
    public function pullByPublication(GetAllPublications $getAllPublications, ImportFromFaceBook $ImportFromFaceBook, EntityManagerInterface $em): Response
    {
        $publications = $getAllPublications->execute();
        $projects = [];
        foreach ($publications as $publication){
            $project = $ImportFromFaceBook->createProjectFromAlbum($publication);
            
            if ($project){
                $projects[]= $project;
                $em->persist($project);
                $em->flush();
            }
            
        }
        


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
    public function admin(FbGraph $fbGraph): Response
    {
        dump($fbGraph->getAlbums());
        return $this->render('admin/index.html.twig');
    }
}