import { Model } from '@vuex-orm/core'
import Jury from "./jury";
import ContestJury from "./contest_jury";
import Contender from "./contender";
import ContenderContest from "./contender_contest";
import Submission from "./submission";
import Vote from "./vote";
import ContestReward from "./contest_reward";

export default class Contest extends Model {
    static entity = 'contests';
    static primaryKey = 'id';

    static fields () {
        return {
            id: this.attr(null),
            balance: this.attr(null),
            title: this.attr(null),
            hash: this.attr(null),
            last_paid: this.attr(null),
            link: this.attr(null),
            max_voting_attempts: this.attr(null),
            jury_id: this.attr(null),

            start_at: this.attr(null),
            end_at: this.attr(null),
            voting_end_at: this.attr(null),

            contenders_reward: this.attr(null),
            juries_reward: this.attr(null),
            jury_reward_percent: this.attr(null),

            jury: this.belongsTo(Jury, 'jury_id', 'id'),
            juries: this.belongsToMany(Jury, ContestJury, 'contest_id', 'jury_id'),
            contenders_contest: this.hasMany(ContenderContest, 'contest_id', 'id'),
            contenders: this.belongsToMany(Contender, ContenderContest, 'contest_id', 'contender_id'),
            submissions: this.hasMany(Submission, 'contest_id', 'id'),
            votes: this.hasMany(Vote, 'contest_id', 'id'),
            contest_rewards: this.hasMany(ContestReward, 'contest_id', 'id'),
        }
    }
}
