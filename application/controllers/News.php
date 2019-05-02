<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:14 AM
 */
defined('BASEPATH') OR exit ('No direct script access allowed');

class News extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //http://161.142.103.225
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {

        $dir = APPPATH . 'third_party/news';
        $scanned = array_diff(scandir($dir), array('..', '.'));

        $news = (array(
            'success' => true,
            'prev' => 1,
            'current' => 1,
            'next' => (sizeof($scanned) / 5 > 1 ? 2 : 1),
            'last' => max(ceil(sizeof($scanned)),1),
            'data' => array()
        ));
        foreach($scanned as $key=>$newsFile)
        {
            $key = $key -1;
            if($key >=3)
            {
                break;
            }
            $newsItem = json_decode(file_get_contents(APPPATH . 'third_party/news/' . $newsFile));
            array_push($news['data'], ($newsItem));
        }
        return $this->jsonIFy($news);
    }

    public function getNews($type)
    {
        $type = $this->uri->segment(2);
        if (is_numeric($type))
        {
            return $this->newsById($type);
        }
        else if($type === 'all')
        {
            return $this->allNews();
        }
    }

    public function allNews()
    {

        $dir = APPPATH . 'third_party/news';
        $scanned = array_diff(scandir($dir), array('..', '.'));

        $news = array();
        foreach($scanned as $newsFile)
        {
            array_push($news,  json_decode(file_get_contents(APPPATH . 'third_party/news/' . $newsFile)));
        }
        return $this->jsonIFy($news);
    }

    public function newsById($id)
    {
        echo "asdsa";
        $id = (int)$id;

        $dir = APPPATH . 'third_party/news';
        $scanned = array_slice(scandir($dir),2);
        $news = (array(
            'success' => true,
            'prev' => max($id-1,1),
            'current' => $id,
            'next' => min($id +1, ceil(sizeof($scanned)/5)),
            'last' => ceil(sizeof($scanned)/5),
            'data' => array()
        ));

        foreach($scanned as $key=>$newsFile)
        {
            if ($key >= ($id - 1) * 5 && $key <= $id* 5) {
                $newsItem = json_decode(file_get_contents(APPPATH . 'third_party/news/' . $newsFile));
                array_push($news['data'], ($newsItem));
            }else {
                echo "false";
            }
        }
        return $this->jsonIFy($news);
    }

    public function jsonIFy(array $data = array())
    {
        echo json_encode($data);
    }
}