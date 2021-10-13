<?php

namespace App\Service\Fbgraph;

use App\Entity\User;
use Doctrine\ORM\Query\AST\Node;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FbgraphQueryBuilder
{
    /** @var User */
    private $user ;

    private $uri ;

    public function __construct(private int $fbPageId, Security $security, private HttpClientInterface $client){

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

}