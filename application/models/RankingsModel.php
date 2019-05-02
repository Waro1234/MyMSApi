<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:42 AM
 */

class RankingsModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllRankings()
    {
        $this->benchmark->mark('code_start2');
        $query =$this->db->query('SELECT * FROM characters3 WHERE gm < 3 ORDER BY rebirths DESC')->result_array();
        $this->benchmark->mark('code_end2');
        echo $this->benchmark->elapsed_time('code_start2', 'code_end2');
        echo "blabk";
        return $query;
    }
}