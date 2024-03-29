<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:14 AM
 */
defined('BASEPATH') OR exit ('No direct script access allowed');

class Rankings extends CI_Controller
{

    private $jobs;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('RankingsModel');
        $this->jobs = json_decode(file_get_contents(APPPATH . 'controllers/Jobs.json'), true);

    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        return $this->jsonIFy($this->getMappedRankings(), 200);
    }

    /**
     * Paged rankings
     * @param $page
     */
    public function page($page)
    {
        // Remove 1 so we do it based on zero
        $page = (int)$this->uri->segment(2) - 1;
        $ranks = $this->getMappedRankings();

        $filter = array_filter($ranks, function ($key) use ($page) {
            // Filter
            return $key >= (0 + ($page ? $page * 10 : 0)) && $key <= (9 + ($page ? $page * 10 : 0));
        }, ARRAY_FILTER_USE_KEY); // Tell the filter to filter based on keys
        return $this->jsonIFy($filter, 200);
    }

    /**
     * @param $type
     * @param $name
     */
    public function jobAndSearch($type, $name)
    {
        $type = $this->uri->segment(2);
        $char = $this->uri->segment(3);

        $ranks = $this->getMappedRankings();
        $filter = array();
        if (strcmp($type, 'job') === 0) {
            $jobs = $this->jobs;
            $filter = array_filter($ranks, function ($item, $key) use ($char, $jobs) {
                return (strcasecmp(strtolower($jobs[$item['job']]['name']), $char) === 0) && ($key >= 0 && $key <= 9);
            }, ARRAY_FILTER_USE_BOTH);
        } else if (strcmp($type, 'search') === 0) {
            $filter = array_filter($ranks, function ($item, $key) use ($char) {
                return (strcasecmp($item['name'], $char) === 0) && ($key >= 0 && $key <= 9);
            }, ARRAY_FILTER_USE_BOTH);
        }
        return $this->jsonIFy($filter, 200);
    }

    public function jobAndSearchPaged($type, $char, $page)
    {
        $type = $this->uri->segment(2);
        $char = $this->uri->segment(3);
        $page = (int)$this->uri->segment(4) - 1;
        $ranks = $this->getMappedRankings();

        $filter = array();
        if (strcmp($type, 'job') === 0) {
            $jobs = $this->jobs;
            $filter = array_filter($ranks, function ($item, $key) use ($char, $jobs, $page) {
                return (strcasecmp(strtolower($jobs[$item['job']]['name']), strtolower($char)) === 0) && ($key >= (0 + ($page ? $page * 10 : 0)) && $key <= (9 + ($page ? $page * 10 : 0)));
            }, ARRAY_FILTER_USE_BOTH);
        } else if (strcmp($type, 'search') === 0) {
            $filter = array_filter($ranks, function ($item, $key) use ($char, $page) {
                return (strcasecmp($item['name'], $char) === 0) && ($key >= (0 + ($page ? $page * 10 : 0)) && $key <= (9 + ($page ? $page * 10 : 0)));

            }, ARRAY_FILTER_USE_BOTH);

        }
        return $this->jsonIFy($filter, 200);
    }

    public function getMappedRankings()
    {
        $rankings = $this->RankingsModel->getAllRankings();
        return array_map(array($this, 'mapRank'), $rankings, array_keys($rankings));
    }

    public function mapRank($ranking, $k)
    {
        $ranking['rank'] = $k + 1;
        return $ranking;
    }

    public function jsonIFy($data, $status)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode(array(
                'data' => $data
            )));
    }
    
}