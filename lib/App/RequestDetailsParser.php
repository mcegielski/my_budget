<?php

namespace App;

class RequestDetailsParser {
    
    const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 100;
    
    const DEFAULT_SORT_BY = "ID";
    const DEFAULT_SORT_HOW = "DESC";
    
    private $requestParams;
    
    public function __construct($request) {
        $this->requestParams = $request->getQueryParams();
    }
    
    public function getLimit(){
        $requestedLimit = @$this->requestParams['limit'] ?: self::DEFAULT_LIMIT;
        if ($requestedLimit <= self::MAX_LIMIT){
            return $requestedLimit;
        }
        return self::MAX_LIMIT;
    }
    
    public function getOffset(){
        $requestedOffset = @$this->requestParams['offset'] ?: 0;
        return $requestedOffset;
    }
    
    public function getOrder(){
        $sortBy = @$this->requestParams['sort_by'] ?: self::DEFAULT_SORT_BY;
        $sortHow = @$this->requestParams['sort_how'] ?: self::DEFAULT_SORT_HOW;
        
        return [$sortBy => $sortHow];
    }
    
}