<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:42 AM
 */

class VoteModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getVoteTimesFromUser($username)
    {
        $username = $this->db->escape($username);
        return $this->db->query('SELECT * FROM votingrecords WHERE account = ' . $username)->row();
    }

    public function newVotingAccount($username, $ip)
    {
        $username = $this->db->escape($username);
        $ip = $this->db->escape($ip);

        $insertRec = array(
            'account' => $username,
            'ip' => $ip,
            'gtopDate' => 0,
            'gtopTimes' => 0,
            'topgDate' => 0,
            'topgTimes' => 0,
            'xtremeDate' => 0,
            'xtremeTimes' => 0,
        );
        $this->db->insert('votingrecords',$insertRec);

        return json_encode(array(
            'gtopDate' => 0,
            'topgDate' => 0,
            'xtremeDate' => 0,
        ));
    }

    public function vote($id, $site,$time) 
    {
        
        $data = array(
            $site . 'Times' => $site . 'Times+1',
            $site . 'Date' => $time
        );

        $this->db->where('id', $id);
        $this->db->update('votingrecords',$data);
    }

}