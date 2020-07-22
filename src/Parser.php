<?php 
namespace AdinanCenci\ScrapingInstagram;

class Parser 
{
    protected $response;

    public function __construct($response) 
    {
        $this->response = $response;
    }

    public function parse() 
    {
        if ($this->response->code < 200 || $this->response->code >= 300) {
            throw new \Exception('Request failed. Code: '.$this->response->code, 1);
            return false;
        }

        if (! $this->isDataPresent($this->response->body)) {
            throw new \Exception('Something went wrong', 1);
            return false;
        }

        if (! $json = $this->extractJson($this->response->body)) {
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

    protected function isDataPresent($string) 
    {
        return (bool) substr_count($string, '{"ProfilePage"');
    }
}
