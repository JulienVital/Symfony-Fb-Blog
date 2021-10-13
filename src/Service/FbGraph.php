<?php 

namespace App\Service;

use App\Service\Fbgraph\FbgraphQueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FbGraph
{

    /**
     * @var User
     */
    private $user;
    
    public function __construct(private int $fbPageId, Security $security, private HttpClientInterface $client, private FbgraphQueryBuilder $fbgraphQueryBuilder){

        $this->user= $security->getUser();
    }

    public function getAlbums() :array
    {
        
        $uri = $this->FbgraphQueryBuilder->createUri("$this->fbPageId/albums",['name']);
    
		return $this->FbgraphQueryBuilder->getResponse()->toArray();
    }


    public function getAllPublication()
    {
        $uri = $this->FbgraphQueryBuilder->createUri("$this->fbPageId/posts",['message','attachments','permalink_url','place']);
        $data = $this->arrayWithoutPagination($uri);
   
        $dataOrderByType=[];
        foreach ($data as $key => $value){

            $type = $value['attachments']['data'][0]['type'] ??null;
            $dataOrderByType[$type][]=$value;

        }
        return $dataOrderByType['album'];
    }

    /**
     * get an uniq array from facebook with only data
     */
    private function arrayWithoutPagination($uri, $data=[]):array{
       
        $nextdata = $this->FbgraphQueryBuilder->getResponse()->toArray();
        $nextpage = $nextdata['paging']['next'] ?? null;

        if (isset($nextpage)){

            $data= array_merge($data,$nextdata['data']);
            $data = $this->arrayWithoutPagination($nextpage, $data);

        }
        return $data;
    }

    public function getImage($node)
    {
        $uri = $this->FbgraphQueryBuilder->createUri("$node",['images']);
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