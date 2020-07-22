<?php 
namespace AdinanCenci\ScrapingInstagram;

class Instagram 
{
    protected $username;

    public function __construct($username) 
    {
        $this->username = $username;
    }

    public function fetch() 
    {
        $request  = new Request( $this->username );
        $parser   = new Parser( $request->request() );
        
        try {
            $images = $parser->parse();
        } catch (\Exception $e) {
            $images = array();
        }

        if ($images) {
            return $images;
        }

        // try again, now with some headers
        $request  = new Request( $this->username );
        $parser   = new Parser( $request->request(), $this->generateRequestHeaders() );

        try {
            $images = $parser->parse();
        } catch (\Exception $e) {
            throw $e;
        }        

        return $images;
    }

    protected function generateRequestHeaders() 
    {
        return array(
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9", 
            $this->generateCookieHeader() 
        );
    }

    protected function generateCookieHeader() 
    {
        $time = time();
        return 'Cookie: sessionid='.$time.'; ds_user_id='.md5( $time );
    }

    /** remove duplicates */
    public static function unique($pictures) 
    {
        return array_map('unserialize', array_unique(array_map('serialize', $pictures)));
    }
}
