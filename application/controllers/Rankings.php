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

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rankingsmodel');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->benchmark->mark('code_start');
        $rankings =$this->rankingsmodel->getAllRankings();
        $mappedRankings = array_map(array($this, 'mapRank'), $rankings, array_keys($rankings));
        $this->benchmark->mark('code_end');
        echo $this->benchmark->elapsed_time('code_start', 'code_end');
        unset($rankings);

        $this->output->set_output($this->jsonIFy($mappedRankings));
//        return $this->jsonIFy($mappedRankings);
    }

    public function mapRank($ranking, $k)
    {
        $ranking['rank'] = $k;
        return $ranking;
    }

    public function jsonIFy(array $data = array())
    {
        echo json_encode($data);
    }
}