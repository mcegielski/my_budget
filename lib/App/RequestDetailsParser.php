<?php

namespace App;

class RequestDetailsParser {
    
    const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 100;
    
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
    
}