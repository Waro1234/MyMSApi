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
        $this->load->model('AccountModel');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        return $this->jsonIFy(array(
            'logged_in' => false,
            'username' => "",
            'gm_level' => 0
        ),200);
    }

    public function server() 
    {
        return $this->jsonIFy(array(
            'server_status' => array(
                array (
                    'name' => 'REST API',
                    'status' => true
                ),
                array(
                    'name' => 'Channel 1',
                    'status' => false
                ),
                array(
                    'name' => 'Gnomed',
                    'status' => true
                )
            ),
            'online_count' => $this->AccountModel->getOnline(),
            'alert' => 'The website is currently under maintenance!'
        ),200);
    }

    public function settings()
    {
        $alert = $this->input->post('alert');
        return $this->jsonIFy(array(
            'success' => true,
            'data' => array(
                'alert' => $alert
            )
        ),200);
    }



    public function jsonIFy($data, $status)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }
}