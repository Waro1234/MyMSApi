<?php
/**
 * Created by PhpStorm.
 * User: thama
 * Date: 5/2/2019
 * Time: 10:14 AM
 */
defined('BASEPATH') OR exit ('No direct script access allowed');

class Vote extends CI_Controller
{

    private $jobs;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('VoteModel');
        $this->load->model('AccountModel');
        $this->jobs = json_decode(file_get_contents(APPPATH . 'controllers/Jobs.json'), true);

    }

    /**
     * Index Page for this controller.
     */
    public function getVotes($username, $ip)
    {
        $username = $this->uri->segment(2);
        $ip = $this->uri->segment(3);
        $ip = str_replace('_', '.',$ip);
        $voteRec = $this->VoteModel->getVoteTimesFromUser($username);
        if ($voteRec === null || sizeof($voteRec) === 0) {
            $voteRec =$this->VoteModel->newVotingAccount($username,$ip);
        }
        $now = $this->millitime();
        return $this->jsonIFy(array(
        'data' => array(
                (($now - (double)$voteRec->gtopDate) >= 86400000),
                (($now - (double)$voteRec->topgDate) >= 43200000),
                (($now - (double)$voteRec->xtremeDate) >= 43200000)
            )
        ),200);
    }

    public function vote($username, $ip, $site)
    {
        $username = $this->uri->segment(2);
        $ip = str_replace('_', '.', $this->uri->segment(3));
        $site = array('gtop','topg','xtreme')[$this->uri->segment(4)];

        $account = $this->AccountModel->getAccount($username);
        if ($account !== null) {
            $accountUpdated = $this->AccountModel->updateVote($username);
            if ($accountUpdated) {
                $voteRec = $this->VoteModel->getVoteTimesFromUser($username);
                if ($voteRec === null || sizeof($voteRec) === 0) {
                    $voteRec =$this->VoteModel->newVotingAccount($username,$ip);
                    $voteRec['gtopTimes'] = 0;
                    $voteRec['topgTimes'] = 0;
                    $voteRec['xtremeTimes'] = 0;
                }
                $canVote = $this->getVotes($username, $ip)[$site]; // extra layer of security
                if($voteRec->id !== null && $canVote) {
                    $this->VoteModel->vote($voteRec->id, $site, $this->millitime());
                    return $this->jsonIFy(array('success' => true),202);
                } else {
                    return $this->jsonIFy(array(
                        'success' => false,
                        'data' => array(
                            'message' => 'Failed to find a vote record'
                        )
                    ), 500);
                }
            } else {
                
                return $this->jsonIFy(array(
                    'success' => false,
                    'data' => array(
                        'message' => 'Failed to update voting record'
                    )
                ), 500);
            }
        } else {
            return $this->jsonIFy(array(
                'success' => false,
                'data' => array(
                    'message' => 'Account doesn\'t exist'
                )
            ), 404);
        }
    }

    private function millitime() {
        $microtime = microtime();
        $comps = explode(' ', $microtime);

        // Note: Using a string here to prevent loss of precision
        // in case of "overflow" (PHP converts it to a double)
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
    private function jsonIFy($data, $status)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data));
    }
}