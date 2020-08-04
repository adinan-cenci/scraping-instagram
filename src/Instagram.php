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
        $request  = new Request( $this->username, $this->generateRequestHeaders() );
        $parser   = new Parser( $request->request() );

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
        foreach ($pictures as $key => $pic) {
            self::removeDuplicate($pictures, $key, $pic);
        }

        return $pictures;
    }

    protected static function removeDuplicate(&$pictures, $key, $pic) 
    {
        foreach ($pictures as $k => $p) {

            if ($k < $key) {
                return;
            }
            
            if ($key == $k) {
                continue;
            }

            if (self::getPath($pic['src']) == self::getPath($p['src'])) {
                unset($pictures[$k]);
                return;
            }
        }
    }

    protected static function getPath($url) 
    {
        $array = parse_url($url);
        return $array['path'];
    }
}
