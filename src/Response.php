<?php 
namespace AdinanCenci\ScrapingInstagram;

class Response 
{
    protected $code;
    protected $header;
    protected $body;

    public function __construct($code, $header, $body) 
    {
        $this->code     = $code;
        $this->header   = $header;
        $this->body     = $body;
    }

    public function __get($var) 
    {
        return $this->{$var};
    }
}
