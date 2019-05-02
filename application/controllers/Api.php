<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:14 AM
 */
defined('BASEPATH') OR exit ('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->cache(60); // Will expire in 60 minutes
        $this->output->enable_profiler(TRUE);
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        return $this->jsonIFy(array("message" => "we are live!"));
    }

    public function jsonIFy(array $data = array())
    {
        echo json_encode($data);
    }
}