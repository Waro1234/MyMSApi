<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:42 AM
 */

class AccountModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOnline()
    {
        return $this->db->query('SELECT COUNT(id) as online FROM accounts where loggedin > 0')->row()->online;
    }

    public function getAccount($username)
    {
        $username = $this->db->escape($username);
        return $this->db->query('SELECT * FROM accounts WHERE name = ' . $username)->row();
    }

    public function accountExists($username) 
    {
        $username = $this->db->escape($username);
        $this->db->select('name');
        return $this->db->get_where('accounts', array('name' => $username)) !== null;
    }

    public function register($username, $password, $bd, $email, $ip)
    {
        $dbValues = array(
            'name' => $username,
            'password' => base64_encode(hex2bin(hash('sha1', $password))),
            'salt' => "NULL",
            'pin' => null,
            'pic' => 111111,
            'loggedin' => 0,
            'lastlogin' => "NULL",
            'createdat' => date('Y - j -n'),
            'birthday' => $bd,
            'banned' => 0,
            'banreason' => "NULL",
            'gm' => 0,
            'macs' => "NULL",
            "nxCredit" => 0,
            "maplepoint" => 0,
            "nxPrepaid" => 0,
            'characterslots' => 5,
            'gender' => 0,
            'tempban' => 0,
            'greason' => "0",
            'tos' => 1,
            'sitelogged' => "NULL",
            'webadmin' => 0,
            'nick' => "NULL",
            'mute' => 0,
            'email' => $email,
            'ip' => $ip,
            'rewardpoints' => 0,
            'hwid' => 0,
            'lastDaily' => 0,
            'votes' => 0
        );
        $this->db->insert('accounts',$dbValues);
        return $this->db->affected_rows() > 0;
    }

    public function login($username)
    {
        $username = $this->db->escape = $username;
        $this->db->select('name, password, gm, votes'); 
        $this->db->from('accounts');   
        $this->db->where('name', $username);
        return $this->db->get()->result();
    }

    public function updateVote($username)
    {
        $username = $this->db->escape($username);
        $this->db->set('votes', 'votes+1', FALSE);
        $this->db->where('name', $username);
        $this->db->update('accounts');
        return true;
    }

}