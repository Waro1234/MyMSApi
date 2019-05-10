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
            'last' => max(ceil(sizeof($scanned)), 1),
            'data' => array()
        ));
        foreach ($scanned as $key => $newsFile) {
            $key = $key - 1;
            if ($key >= 3) {
                break;
            }
            $newsItem = json_decode(file_get_contents(APPPATH . 'third_party/news/' . $newsFile));
            array_push($news['data'], ($newsItem));
        }
        return $this->jsonIFy($news, '200');
    }

    public function getNews($type)
    {
        $type = $this->uri->segment(2);
        if (is_numeric($type)) {
            return $this->newsById($type);
        } else if ($type === 'all') {
            return $this->allNews();
        }
    }

    public function allNews()
    {

        $dir = APPPATH . 'third_party/news';
        $scanned = array_diff(scandir($dir), array('..', '.'));

        $news = array();
        foreach ($scanned as $newsFile) {
            array_push($news, json_decode(file_get_contents(APPPATH . 'third_party/news/' . $newsFile)));
        }
        return $this->jsonIFy($news, '200');
    }

    public function newsById($id)
    {
        $id = (int)$id;

        $dir = APPPATH . 'third_party/news';
        $scanned = array_slice(scandir($dir), 2);
        $news = (array(
            'success' => true,
            'prev' => max($id - 1, 1),
            'current' => $id,
            'next' => $id + 1,
            'last' => sizeof($scanned),
            'data' => array()
        ));

        try {
            $newsItem = json_decode(file_get_contents(APPPATH . 'third_party/news/' . $id . '.json'));
            if ($newsItem !== null) {
                array_push($news['data'], ($newsItem));
                return $this->jsonIFy($news, '200');
            } else {
                return $this->jsonIFy($news, '404');
            }
        } catch (Exception $ex) {
            return $this->jsonIFy($news, '500');
        }
    }

    public function editPost($id){
        $id = (int)$this->uri->segment(2);

        $filePath = APPPATH . 'third_party/news/' . $id . '.json';
        $newsItem = json_decode(file_get_contents($filePath),true);
        if($newsItem !== null){
            $newsItem['views']++;
            file_put_contents($filePath,json_encode($newsItem));
            return $this->jsonIFy(array(
                "success" => true,
                "data" => $newsItem
            ),200);
        } else {
            return $this->jsonIFy(array(
                "success" => false,
                "error" => "Post does not exist"
            ),404);
        }
    }

    public function jsonIFy($data, $status)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }
}