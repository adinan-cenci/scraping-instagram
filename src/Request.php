<?php 
namespace AdinanCenci\ScrapingInstagram;

class Request 
{
    protected $username = null;
    protected $headers  = null;

    public function __construct($username, $headers = null) 
    {
        $this->username = $username;
        $this->headers  = $headers;
    }
    
    public function request() 
    {
        $options = array(
            CURLOPT_URL             => "https://www.instagram.com/$this->username/", 
            CURLOPT_SSL_VERIFYPEER  => false, // don't verify certificate
            CURLOPT_RETURNTRANSFER  => true,  // return content
            CURLOPT_FOLLOWLOCATION  => true,  // follow redirects
            CURLOPT_HEADER          => true   // return headers
        );

        if ($this->headers) {
            $options[CURLOPT_HTTPHEADER] = $this->headers;
        }

        //--------------

        $ch         = curl_init();
        curl_setopt_array($ch, $options);
        $response   = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header     = substr($response, 0, $headerSize);
        $body       = substr($response, $headerSize);

        curl_close($ch);

        //--------------

        return new Response($httpCode, $header, $body);
    }
}
