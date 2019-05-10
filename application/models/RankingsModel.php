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
        return $this->db->query('SELECT * FROM characters WHERE gm < 3 ORDER BY rebirths DESC')->result_array();
    }

}