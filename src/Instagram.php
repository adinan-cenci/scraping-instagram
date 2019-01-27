<?php 
namespace AdinanCenci\ScrapingInstagram;

class Instagram 
{
    protected $username;

    public function __construct($username) 
    {
        $this->username = $username;
    }

    public function get($thumbSize = 1) 
    {
        $html = $this->makeRequest($this->username);
        if (! $json = $this->extractJson($html)) {
            throw new \Exception('Unable to get pictures', 1);            
            return false;
        }        
        $data = json_decode($json, true);
        return $this->getPictures($data, $thumbSize);
    }

    protected function makeRequest($userName) 
    {
        $ch = curl_init("https://www.instagram.com/$userName/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    protected function extractJson($html) 
    {
        if (! preg_match('#<script type="text/javascript">window\._sharedData = (.*);</script>#', $html, $matches)) {
            return null;
        }

        return $matches[1];
    }
    
    protected function getPictures($data, $thumbSize = 1) 
    {
        $thumbnails = array();
        foreach ($data['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] as $edge) {
            
            if ($edge['node']['__typename'] != 'GraphImage') {
                continue;
            }
            // if there is no thumbnail available, fallsback to a smaller one
            $x = $thumbSize;
            while ($x > 0 and !isset($edge['node']['thumbnail_resources'][$x])) {
                $x--;
            }
            $thumbnails[] = $edge['node']['thumbnail_resources'][$x]['src'];
        }

        return $thumbnails;
    }
}
