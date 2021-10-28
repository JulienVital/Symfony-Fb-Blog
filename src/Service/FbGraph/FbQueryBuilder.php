<?php

namespace App\Service\FbGraph;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FbQueryBuilder
{
    /** @var User */
    private $user ;

    private $uri ;

    public function __construct(Security $security, private HttpClientInterface $client){

        $this->user= $security->getUser();
    }

    /**
     * $ressource path of ressource fbGraph
     * $fieldsRaw array with optional params fbgraph
     */
    public function createUri(string $ressource, array $fieldsRaw=[]) :string
    {   
        $uri = "https://graph.facebook.com/".$ressource."?";
        
        if (!empty($fieldsRaw)){

            $uri .="fields=".implode(',',$fieldsRaw).'&';
        }
        $uri .="access_token=".$this->user->getPageToken();
        $this->uri = $uri ;

        return $uri;
    }

    public function getResponse()
    {
        return $this->client->request('GET',$this->uri);
    }



    /**
     * Set the value of uri
     */
    public function setUri($uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}