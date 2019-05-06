<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:14 AM
 */
defined('BASEPATH') OR exit ('No direct script access allowed');

class Accounts extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('accountmodel');
    }

    public function register() 
    {
        // username, password, password_confirmation, email, ip, bd (fomart = YYYY - M - D)
        // $accountExists =
        
        $date = date('Y - j - n', strtotime($_POST['bd']));
        if($date === '1970 - 1 - 1') {
            return $this->jsonIFy(array(
                'success' => false,
                'error' => 'Please fill in your birthday properly'
            ), 400); //400 = bad request
        }

        if($_POST['password'] !== $_POST['password_confirmation']) {
            return $this->jsonIFy(array(
                'success' => false,
                'error' => 'Password and confirmation password do not match'
            ), 400); //400 = bad request
        } 

        $exists = $this->accountmodel->accountExists($_POST['username']);
        if(!$exists) {
            $inserted = $this->accountmodel->register($this->input->post('username'), $this->input->post('password'), $date, $this->input->post('email'), $this->input->post('ip'));
            if($inserted) {
                return $this->jsonIFy(array(
                    'success' => true
                ),201); // 201 = created
            } else {
                return $this->jsonIFy(array(
                    'success' => false,
                    'error' => 'Something went wrong creating your account, please contact an administrator'
                ), 500);
            }
        } else {
            return $this->jsonIFy(array(
                'success' => false,
                'error' => 'Username is already taken'
            ), 409); //409 = conflict
        }
    }

    public function login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $account = $this->accountmodel->login($username);
        if (!empty($account)) {
            $encrypted = base64_encode(hex2bin(hash('sha1', $password)));
            if ($encrypted === $account[0]->password) {
                return $this->jsonIFy(array(
                    'success' => true,
                    'data' => array(
                        'logged_in' => true,
                        'username' => $account[0]->name,
                        'gm_level' => $account[0]->gm,
                        'votes' => $account[0]->votes
                    )
                ), 200);
            } else {
                return $this->jsonIFy(array(
                    'success' => false,
                    'error' => 'Incorrect password'
                ), 205); // wrong pass
            }
        } else {
            return $this->jsonIFy(array(
                'success' => false,
                'error' => 'Incorrect username'
            ),404);
        }
    }

    public function logout() 
    {
        return $this->jsonIFy(array(
            'success' => true,
            'data' => array(
                'logged_in' => false,
                'username' => '',
                'gm_level' => 0,
            )
        ), 200);
    }

    private function jsonIFy($data, $status)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }
}