import {abiContract} from "@tonclient/core";
import * as contestsAbi from "./abi/contests.json";
import Rating from "../Rating";

export default class Client {
    constructor(client) {
        this.client = client;
        this.abi = abiContract(contestsAbi);
    }

    async getContestsCount (
        {
            code_hash = {eq: "5daea8b855140d110ab07d430883bfecdd4cba9bcded8968fae7fa6cdb5adfbd"},
        } = {}
    ) {
        return await this.client.net.aggregate_collection({
            collection: 'accounts',
            filter: {
                code_hash: code_hash,
            },
            fields: [{field: 'id', fn: 'COUNT'}]
        }).then(({ values }) => values[0]);
    }

    async getContestsByChunk (
        chunk = 10,
        {
            id = {ne: ''},
            code_hash = {eq: "5daea8b855140d110ab07d430883bfecdd4cba9bcded8968fae7fa6cdb5adfbd"},
            last_paid = {gt: 0},
        } = {}
    ) {
        let contests = [];

        let accounts = await this.client.net.query_collection({
            collection: 'accounts',
            filter: {
                id: id,
                code_hash: code_hash,
                last_paid: last_paid,
            },
            limit: chunk,
            order: [{path: "last_paid", direction: "DESC"}],
            result: 'id, balance, boc, last_paid'
        }).then(({ result }) => result);

        for (let account_index in accounts) {
            let contest_info = await this.call(accounts[account_index].id, accounts[account_index].boc, "getContestInfo");
            let contest_to_start = await this.call(accounts[account_index].id, accounts[account_index].boc, "contestStartCountdown");
            let contest_to_end = await this.call(accounts[account_index].id, accounts[account_index].boc, "contestCountdown");
            let contest_to_voting_end = await this.call(accounts[account_index].id, accounts[account_index].boc, "votingCountdown");

            let contenders_info = await this.call(accounts[account_index].id, accounts[account_index].boc, "getContendersInfo");

            /* Set contest info */
            contest_info.decoded.output.juryAddresses.forEach((jury, index, array) => {array[index] = {id: jury}});
            let now = Date.now()/1000;
            let contest = {
                id: accounts[account_index].id,
                balance: accounts[account_index].balance,
                title: this._hexToAscii(contest_info.decoded.output.title),
                hash: contest_info.decoded.output.hash,
                last_paid: accounts[account_index].last_paid,
                link: this._hexToAscii(contest_info.decoded.output.link),
                max_voting_attempts: contest_info.decoded.output.maxVotingAttempts,
                jury_id: contest_info.decoded.output.juryAddr,
                /*jury_keys: contest_info.decoded.output.juryKeys,*/

                start_at: now+(Number(contest_to_start.decoded.output.secondsLeft)),
                end_at: now+(Number(contest_to_end.decoded.output.secondsLeft)),
                voting_end_at: now+(Number(contest_to_voting_end.decoded.output.secondsLeft)),

                jury: {id: contest_info.decoded.output.juryAddr},
                juries: contest_info.decoded.output.juryAddresses,
            };

            /* Set contest contenders */
            let contenders = [];
            let submissions = [];
            let votes = [];
            let contest_stats = [];
            for (let contender_index in contenders_info.decoded.output.addrs) {
                let contest_stat = await this.call(
                    accounts[account_index].id,
                    accounts[account_index].boc,
                    "getStatsFor",
                    {id: contenders_info.decoded.output.ids[contender_index]}
                    );

                let votes_per_juror = await this.call(
                    accounts[account_index].id,
                    accounts[account_index].boc,
                    "getVotesPerJuror",
                    {id: contenders_info.decoded.output.ids[contender_index]}
                );

                for (let index in votes_per_juror.decoded.output.jurorsFor) {
                    let vote = {
                        contest_id: accounts[account_index].id,
                        submission_id: contenders_info.decoded.output.ids[contender_index],
                        jury_id: votes_per_juror.decoded.output.jurorsFor[index],
                        type: 1,
                        type_text: 'accepted',
                        mark: votes_per_juror.decoded.output.marks[index],
                        comment: this._hexToAscii(votes_per_juror.decoded.output.commentsFor[index]),
                    };
                    votes.push(vote);
                }

                for (let index in votes_per_juror.decoded.output.jurorsAbstained) {
                    let vote = {
                        contest_id: accounts[account_index].id,
                        submission_id: contenders_info.decoded.output.ids[contender_index],
                        jury_id: votes_per_juror.decoded.output.jurorsAbstained[index],
                        type: 0,
                        type_text: 'abstained',
                        mark: null,
                        comment: this._hexToAscii(votes_per_juror.decoded.output.commentsAbstained[index]),
                    };
                    votes.push(vote);
                }

                for (let index in votes_per_juror.decoded.output.jurorsAgainst) {
                    let vote = {
                        contest_id: accounts[account_index].id,
                        submission_id: contenders_info.decoded.output.ids[contender_index],
                        jury_id: votes_per_juror.decoded.output.jurorsAgainst[index],
                        type: -1,
                        type_text: 'rejected',
                        mark: null,
                        comment: this._hexToAscii(votes_per_juror.decoded.output.commentsAgainst[index]),
                    };
                    votes.push(vote);
                }

                contenders[contender_index] = {
                    contender_id: contenders_info.decoded.output.addrs[contender_index],
                    contest_id: accounts[account_index].id,
                };

                submissions[contender_index] = {
                    id: contenders_info.decoded.output.ids[contender_index],
                    contest_id: accounts[account_index].id,
                    address: contenders_info.decoded.output.addrs[contender_index],
                    contact: contenders_info.decoded.output.contacts[contender_index],
                    applied_at: contenders_info.decoded.output.appliedAts[contender_index],
                    file_link: this._hexToAscii(contenders_info.decoded.output.fileLinks[contender_index]),
                    forum_link: this._hexToAscii(contenders_info.decoded.output.forumLinks[contender_index]),
                    hash: contenders_info.decoded.output.hashes[contender_index],

                    total_mark: contest_stat.decoded.output.totalPoints,
                    avg_mark: Number(contest_stat.decoded.output.avgHi + '.' + contest_stat.decoded.output.avgLo),
                    accepted: contest_stat.decoded.output.accepted,
                    abstained: contest_stat.decoded.output.abstained,
                    rejected: contest_stat.decoded.output.rejected,
                };
                contest_stats.push(contest_stat.decoded.output);
            }
            contest.contenders = contenders.map((contender) => { return {id: contender.contender_id} });
            contest.contenders_contest = contenders;
            contest.submissions = submissions;
            contest.votes = votes;

            console.log(contest);
            contests.push(contest);
        }

        return contests;
    }

    async call(address, boc, function_name, input = {}) {
        let message = await this.client.abi.encode_message({
            abi: this.abi,
            address: address,
            call_set: {
                function_name: function_name,
                input: input,
            },
            signer: {type: 'None'}
        }).then(({ message }) => message);

        return await this.client.tvm.run_tvm({
            message,
            account:boc,
            abi: this.abi,
        });
    }

    _hexToAscii(str1 = '') {
        var hex  = str1.toString();
        var str = '';
        for (var n = 0; n < hex.length; n += 2) {
            str += String.fromCharCode(parseInt(hex.substr(n, 2), 16));
        }
        return str;
    }
}
