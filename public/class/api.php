<?php
namespace Akut;

class Api{

    private $comic_json_obj = null;
    private $safeTitle = "";
    private $title = "";
    private $description = "";
    private $imgLink = "";
    private $image = null;

    public function __construct()
    {
        //basically replicating random button found on xkcd.com
        $url = 'https://c.xkcd.com/random/comic/';
        $fp = fopen($url, 'r');
        $meta_data = stream_get_meta_data($fp);
        $comic_url = $meta_data['wrapper_data'][7];
        //get id of the mail
        $comic_url = substr($comic_url, 10);
        $comic_url_json = $comic_url . '/info.0.json';

        // send id with url to xkcd api and get info
        $comic_response = file_get_contents($comic_url_json);
        // now we have xkcd api response
        $comic_json_obj = json_decode($comic_response);
        $this->comic_json_obj = $comic_json_obj;

        // setting api data to  class data memebers
        $this->title = $comic_json_obj->title;
        $this->imgLink = $comic_json_obj->img;
        $this->safeTitle = $comic_json_obj->safe_title;
        $this->description = $comic_json_obj->alt;
    }

    public function getImage(){
        if ($this->image != null){
            return $this->image;
        }else{
            //encoding the image for transmission
            $image = base64_encode(file_get_contents($this->comic_json_obj->img));
            $this->image = $image;
            return $image;
        }
    }

    public function getSafetitle(){
        return $this->safeTitle;
    }
    public function getTitle(){
        return $this->title;
    }
    public function getDescription(){
        return $this->description;
    }
    public function getImageLink(){
        // TO:DO
        // no link; then return new xkcd comic link
        return $this->imgLink;
    }

    public function getImageFilesize(){

        $image_headers = get_headers($this->comic_json_obj->img, true);
        $image_filesize = $image_headers['Content-Length'];
        return $image_filesize;
    }

}
