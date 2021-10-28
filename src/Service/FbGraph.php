<?php 

namespace App\Service;

use App\Service\FbGraph\FbQueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FbGraph
{

    /**
     * @var User
     */
    private $user;
    
    public function __construct(private int $fbPageId, Security $security, private HttpClientInterface $client, private FbQueryBuilder $fbQueryBuilder){

        $this->user= $security->getUser();
    }

    public function getAlbums()
    {
        
        //$uri = $this->FbgraphQueryBuilder->createUri("$this->fbPageId/albums",['name']);
    
		//return $this->FbgraphQueryBuilder->getResponse()->toArray();
    }






    public function getImage($node)
    {
        $uri = $this->fbQueryBuilder->createUri("$node",['images']);
        $dataImages = $this->client->request('GET',$uri)->toArray();
   
        return current($dataImages['images'])['source'];
    }


}

/*
       $file = file_get_contents($fbgraph->getImage());
        file_put_contents($images_Directory.'test',$file);
        
        $file = new UploadedFile($images_Directory.'test', 'name of the file');
        dump($file);
*/