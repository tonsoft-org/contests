<?php

namespace App\Classes\FreeTon;

use Extraton\TonClient\Boc;
use Extraton\TonClient\Entity\Abi\AbiType;
use Extraton\TonClient\Entity\Abi\CallSet;
use Extraton\TonClient\Entity\Abi\Signer;
use Extraton\TonClient\Entity\Net\Filters;
use Extraton\TonClient\Entity\Net\OrderBy;
use Extraton\TonClient\Entity\Net\ParamsOfQueryCollection;
use Extraton\TonClient\TonClient;

class Client
{
    public $ton_client = null;
    public $net = null;
    public $abi = null;
    public $tvm = null;

    public function __construct()
    {
        $this->ton_client = new TonClient([
            'network' => [
                'server_address' => 'main.ton.dev'
            ]
        ]);
        $this->net = $this->ton_client->getNet();
        $this->abi = $this->ton_client->getAbi();
        $this->tvm = $this->ton_client->getTvm();
    }

    public function getContests($limit = 10, $filter = [], $order = null, $desc = false): array
    {
        $contests = [];

        $query = new ParamsOfQueryCollection('accounts');
        $query->addResultField('id, balance, boc, last_paid');
        $query->addFilter('code_hash', 'eq', '5daea8b855140d110ab07d430883bfecdd4cba9bcded8968fae7fa6cdb5adfbd');
        foreach ($filter as $f) {
            $query->addFilter($f[0], $f[1], $f[2]);
        }
        if ($order) {
            $query->addOrderBy($order, ($desc) ? OrderBy::DESC : OrderBy::ASC);
        }
        $query->setLimit($limit);
        $accounts = $this->net->queryCollection($query);
        $accounts = $accounts->getResult();

        foreach ($accounts as $account) {
            $contest_info = $this->call($account['id'], $account['boc'], "getContestInfo")->getResponseData();
            $contest_to_start = $this->call($account['id'], $account['boc'], "contestStartCountdown")->getResponseData();
            $contest_to_end = $this->call($account['id'], $account['boc'], "contestCountdown")->getResponseData();
            $contest_to_voting_end = $this->call($account['id'], $account['boc'], "votingCountdown")->getResponseData();
            $contenders_info = $this->call($account['id'], $account['boc'], "getContendersInfo")->getResponseData();

            foreach ($contest_info['decoded']['output']['juryAddresses'] as &$array) {
                $array = ["id" => $array];
            }

            $contest = [
                'id' => $account['id'],
                'balance' => hexdec($account['balance'])/1000000000,
                'title' => hex2bin($contest_info['decoded']['output']['title']),
                'hash' => $contest_info['decoded']['output']['hash'],
                'last_paid' => $account['last_paid'],
                'link' => hex2bin($contest_info['decoded']['output']['link']),
                'max_voting_attempts' => $contest_info['decoded']['output']['maxVotingAttempts'],
                'jury_id' => $contest_info['decoded']['output']['juryAddr'],

                'start_at' => time()+($contest_to_start['decoded']['output']['secondsLeft']),
                'end_at' => time()+($contest_to_end['decoded']['output']['secondsLeft']),
                'voting_end_at' => time()+($contest_to_voting_end['decoded']['output']['secondsLeft']),

                'jury' => ['id' => $contest_info['decoded']['output']['juryAddr']],
                'juries' => $contest_info['decoded']['output']['juryAddresses'],
            ];

            $contenders = [];
            $submissions = [];
            $votes = [];
            $contest_stats = [];
            foreach ($contenders_info['decoded']['output']['addrs'] as $contender_index=>$contender_info) {
                $contest_stat = $this->call(
                    $account['id'],
                    $account['boc'],
                    "getStatsFor",
                    ['id' => $contenders_info['decoded']['output']['ids'][$contender_index]]
                )->getResponseData();

                $votes_per_juror = $this->call(
                    $account['id'],
                    $account['boc'],
                    "getVotesPerJuror",
                    ['id' => $contenders_info['decoded']['output']['ids'][$contender_index]]
                )->getResponseData();


                foreach ($votes_per_juror['decoded']['output']['jurorsFor'] as $vote_index => $vote_per_juror) {
                    $vote = [
                        'contest_id' =>  $account['id'],
                        'submission_id' => $contenders_info['decoded']['output']['ids'][$contender_index],
                        'jury_id' => $vote_per_juror,
                        'type' => 1,
                        'type_text' => 'accepted',
                        'mark' => $votes_per_juror['decoded']['output']['marks'][$vote_index],
                        'comment' => hex2bin($votes_per_juror['decoded']['output']['commentsFor'][$vote_index]),
                    ];
                    $votes[] = $vote;
                }

                foreach ($votes_per_juror['decoded']['output']['jurorsAbstained'] as $vote_index => $vote_per_juror) {
                    $vote = [
                        'contest_id' =>  $account['id'],
                        'submission_id' => $contenders_info['decoded']['output']['ids'][$contender_index],
                        'jury_id' => $vote_per_juror,
                        'type' => 0,
                        'type_text' => 'abstained',
                        'mark' => null,
                        'comment' => hex2bin($votes_per_juror['decoded']['output']['commentsAbstained'][$vote_index]),
                    ];
                    $votes[] = $vote;
                }

                foreach ($votes_per_juror['decoded']['output']['jurorsAgainst'] as $vote_index => $vote_per_juror) {
                    $vote = [
                        'contest_id' =>  $account['id'],
                        'submission_id' => $contenders_info['decoded']['output']['ids'][$contender_index],
                        'jury_id' => $vote_per_juror,
                        'type' => -1,
                        'type_text' => 'rejected',
                        'mark' => null,
                        'comment' => hex2bin($votes_per_juror['decoded']['output']['commentsAgainst'][$vote_index]),
                    ];
                    $votes[] = $vote;
                }

                $contenders[$contender_index] = [
                    'contender_id' => $contenders_info['decoded']['output']['addrs'][$contender_index],
                    'contest_id' => $account['id'],
                ];

                $submissions[$contender_index] = [
                    'id' => $contenders_info['decoded']['output']['ids'][$contender_index],
                    'contest_id' => $account['id'],
                    'address' => $contenders_info['decoded']['output']['addrs'][$contender_index],
                    'contact' => $contenders_info['decoded']['output']['contacts'][$contender_index],
                    'applied_at' => $contenders_info['decoded']['output']['appliedAts'][$contender_index],
                    'file_link' => hex2bin($contenders_info['decoded']['output']['fileLinks'][$contender_index]),
                    'forum_link' => hex2bin($contenders_info['decoded']['output']['forumLinks'][$contender_index]),
                    'hash' => $contenders_info['decoded']['output']['hashes'][$contender_index],

                    'total_mark' => $contest_stat['decoded']['output']['totalPoints'],
                    'avg_mark' => floatval($contest_stat['decoded']['output']['avgHi'].'.'.$contest_stat['decoded']['output']['avgLo']),
                    'accepted' => $contest_stat['decoded']['output']['accepted'],
                    'abstained' => $contest_stat['decoded']['output']['abstained'],
                    'rejected' => $contest_stat['decoded']['output']['rejected'],
                ];
                $contest_stats[] = $contest_stat['decoded']['output'];
            }

            $contest['contenders'] = array_map(function ($contender) { return ['id' => $contender['contender_id']];}, $contenders);
            $contest['contenders_contest'] = $contenders;
            $contest['submissions'] = $submissions;
            $contest['votes'] = $votes;

            $contests[] = $contest;
        }

        return $contests;
    }

    public function call($address, $boc, $function_name, $input = [])
    {
        $call_set = (new CallSet($function_name));
        if (count($input)) {
            $call_set->withInput($input);
        }

        $message = $this->abi->encodeMessage(
            AbiType::fromJson(file_get_contents(__DIR__."/contests.json")),
            (Signer::fromNone()),
           null,
            $call_set,
            $address
        );

        return $this->tvm->runTvm(
            $message->getMessage(),
            $boc,
            null,
            AbiType::fromJson(file_get_contents(__DIR__."/contests.json")),
        );
    }
}
