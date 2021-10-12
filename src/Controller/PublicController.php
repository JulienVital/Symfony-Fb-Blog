<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{


     /**
      * @Route("/", name="home")
      * @Route("/{id}", name="page", requirements={"id"="\d+"})
     */
    public function page(int $id=50): Response
    {
 
        $arrTrans =[];
        for ($i =0; $i < $id; $i++){
            $arrTrans[]='https://picsum.photos/800';
        }

        return $this->render('public/page.html.twig', [
            'arrTrans' => $arrTrans
        ]);
    }

    /**
      * @Route("/test", name="test")
     */
    public function test(ImageRepository $imageRepository): Response
    {
 
        $all = array_column($imageRepository->findAllurl(),'url');


        return $this->render('public/page.html.twig', [
            'arrTrans' => $all
        ]);
    }
}
