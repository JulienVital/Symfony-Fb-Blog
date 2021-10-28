<?php 

namespace App\Service;

use App\Entity\Image;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportFromFaceBook
{
    public function __construct(ProjectRepository $projectRepository, SluggerInterface $slugger, private $images_Directory, private FbGraph $fbgraph, private EntityManagerInterface $em)
    {
        $this->projectRepository = $projectRepository;
        $this->slugger = $slugger;
        
    }

    public function createProjectFromAlbum($rawProject){
        
        $id = $rawProject['id'];
        $project = $this->projectRepository->findById($id);
        if ($project){
            return null;
        }
            $project = new Project();
            $project->setFacebookId($id);
            $project->setTitle($rawProject["message"]??uniqid());
            $project->setSlug($this->slugger->slug($project->getTitle())??uniqid());
        foreach ($rawProject['attachments']['data'][0]['subattachments']['data'] as $value){
            if (!$project->getImageShowcase()){
                $project->setImageShowcase($this->createImageFromId($value['target']['id']));
            }else{

                $project->addImage($this->createImageFromId($value['target']['id']));
            }
        }
        return $project;

    }

    private function createImageFromId(string $imageId):Image{

        $image = new Image();
        $this->em->persist($image);

        $file= file_get_contents($this->fbgraph->getImage($imageId));

        $image->setFacebookId($imageId);
        
        $image->setUrl('photos/'.$imageId.".jpg");

        file_put_contents($image->getUrl(),$file);
        
        return $image;
    }
}