<?php

namespace App\Service\FbGraph;


class GetAllPublications
{
    public function __construct(private FbQueryBuilder $fbQueryBuilder, private int $fbPageId)
    {
    }
    
    public function execute()
    {
        $uri = $this->fbQueryBuilder->createUri("$this->fbPageId/posts",['message','attachments','permalink_url','place']);
        $data = $this->arrayWithoutPagination($uri);
   
        $dataOrderByType=[];
        foreach ($data as $value){

            $type = $value['attachments']['data'][0]['type'] ??null;
            $dataOrderByType[$type][]=$value;

        }
        return $dataOrderByType['album'];
    }

    /**
     * get an uniq array from facebook with only data
     */
    private function arrayWithoutPagination($uri, $data=[]):array{
        $nextdata = $this->fbQueryBuilder->getResponse()->toArray();
        $nextpage = $nextdata['paging']['next'] ?? null;

        if (isset($nextpage)){

            $data= array_merge($data,$nextdata['data']);
            $this->fbQueryBuilder->setUri($nextpage) ;
            $data = $this->arrayWithoutPagination($nextpage, $data);

        }
        return $data;
    }

}