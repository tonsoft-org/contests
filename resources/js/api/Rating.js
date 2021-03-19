import * as FileSaver from "file-saver";
import * as XLSX from "xlsx";

export default class Rating {
    contest_id = null;
    submissions = [];
    rewards = [];

    constructor (contest_id, rewards = []) {
        this.contest_id = contest_id;
        this.rewards = rewards;
    }

    addSubmission (submission_id, contender_id, total_mark, avg_mark, accepted, abstained, rejected) {
        let accepted_percent = 0;
        if (accepted > 0) {
            accepted_percent = (Number(accepted) * 100) / (Number(accepted)+Number(rejected));
        }

        this.submissions.push({
            submission_id,
            contender_id,
            total_mark,
            avg_mark: avg_mark.toFixed(2),
            accepted_percent: accepted_percent.toFixed(2),
            accepted,
            abstained,
            rejected,
        });
    }

    /**
     * @param {array}   contenders  Result of getContendersInfo
     * @param {array}   stats       Result of getStatsFor
     */
    addSubmissionsBySmartContractFunctions (contenders, stats) {
        for (let index in stats) {
            this.addSubmission(
                contenders.ids[index],
                contenders.addrs[index],
                stats[index].totalPoints,
                Number(stats[index].avgHi +'.'+ stats[index].avgLo),
                stats[index].accepted,
                stats[index].abstained,
                stats[index].rejected,
            );
        }

        return this;
    }

    getResult (logic_type = 0, rewards = null) {
        if (rewards !== null) {
            this.buildRewardsArray(rewards);
        }

        let indexes = [];
        let result = [];
        let rejected = [];
        if (logic_type === 0) {
            indexes = Object.keys(this.submissions).sort((a,b) => {return this.submissions[b].avg_mark-this.submissions[a].avg_mark;});
        } else {
            indexes = Object.keys(this.submissions).sort((a,b) => {return this.submissions[b].accepted_percent - this.submissions[a].accepted_percent;});
        }

        for (let index in indexes) {
            if (this.submissions[indexes[index]]['rejected'] > 0 && this.submissions[indexes[index]]['accepted_percent'] < 50) {
                rejected.push({
                    ...{rating: 'rejected'},
                    ...this.submissions[indexes[index]],
                    ...{reward: this.getRewardByRating(Number(index)+1)},
                });
            } else {
                result.push({
                    ...{rating: result.length+1},
                    ...this.submissions[indexes[index]],
                    ...{reward: this.getRewardByRating(Number(index)+1)},
                });
            }
        }

        return result.concat(rejected);
    }

    buildRewardsArray (rewards) {
        if (rewards.length === 0) {
            return this.rewards = [];
        }

        if (!isNaN(rewards[0])) {
            return this.rewards = rewards.map((reward, index) => { return {rating: index+1, reward: reward}});
        } else if (
            typeof rewards[0] === "object" &&
            !isNaN(rewards[0].rating) &&
            !isNaN(rewards[0].reward)
        ) {
            return this.rewards = rewards;
        }

        return this.rewards = [];
    }

    getRewardByRating (rating) {
        for (let index in this.rewards) {
            if (this.rewards[index].rating === rating) {
                return Number(this.rewards[index].reward);
            }
        }

        return null;
    }

    getJuryResult (juries, reward_percent, contest_rewards) {
        let total_reward = contest_rewards.reduce((accumulator, current) => accumulator + Number(current.reward), 0);
        let total_votes = 0;
        for (let jury_index in juries) {
            juries[jury_index].votes_count = 0;

            for (let vote_index in juries[jury_index].votes) {
                if (
                    juries[jury_index].votes[vote_index].type !== 0
                ) {
                    juries[jury_index].votes_count++;
                    total_votes++;
                }
            }
        }

        return juries.map((jury) => {
            return {
                jury_id: jury.id,
                reward: (total_votes !== 0) ? ((jury.votes_count) / total_votes) * total_reward : 0,
            };
        });
    }
}
