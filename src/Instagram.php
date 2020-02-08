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
        $html = self::makeRequest($this->username, array(), $httpCode);

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception('Request failed. Code: '.$httpCode, 1);
            return false;
        }

        if (! $this->isDataPresent($html)) {
            // something went wrong, lets try again with some new headers
            $html = self::makeRequest($this->username, $this->generateRequestHeaders(), $httpCode);  
        }

        if (! $this->isDataPresent($html)) {
            throw new \Exception('Something went wrong', 1);
            return false;
        }

        if (! $json = $this->extractJson($html)) {
            throw new \Exception('Unable to extract data', 1);
            return false;
        }

        $data = json_decode($json, true);
        return $this->getPictures($data);
    }

    protected function extractJson($html) 
    {
        if (! preg_match('#<script type="text/javascript">window\._sharedData = (.*);</script>#', $html, $matches)) {
            return null;
        }

        return $matches[1];
    }
    
    protected function getPictures($data) 
    {
        $pictures = array();

        $user = $data['entry_data']['ProfilePage'][0]['graphql']['user'];

        foreach ($user['edge_owner_to_timeline_media']['edges'] as $edge) {

            if (! in_array($edge['node']['__typename'], array('GraphImage', 'GraphSidecar', 'GraphVideo'))) {
                continue;
            }

            $caption    = $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'];
            $src        = $edge['node']['display_url'];
            $thumbnails = array();

            foreach ($edge['node']['thumbnail_resources'] as $thumb) {
                $key = $thumb['config_width'].'x'.$thumb['config_height'];
                $thumbnails[$key] = $thumb['src'];
            }            

            $pictures[] = array(
                'src'           => $src, 
                'caption'       => $caption, 
                'thumbnails'    => $thumbnails
            );
        }

        return $pictures;
    }

    public static function makeRequest($userName, $headers = array(), &$httpCode = '') 
    {
        $options = array(
            CURLOPT_URL             => "https://www.instagram.com/$userName/", 
            CURLOPT_SSL_VERIFYPEER  => false, // don't verify certificate
            CURLOPT_RETURNTRANSFER  => true,  // return content
            CURLOPT_FOLLOWLOCATION  => true   // follow redirects
        );

        if ($headers) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }        

        //--------------

        $ch         = curl_init();
        curl_setopt_array($ch, $options);
        $content    = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //--------------

        return $content;
    }

    protected function isDataPresent($string) 
    {
        return (bool) substr_count($string, '{"ProfilePage"');
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
