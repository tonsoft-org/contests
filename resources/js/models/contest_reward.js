import { Model } from '@vuex-orm/core'

export default class ContestReward extends Model {
    static entity = 'contest_rewards';
    static primaryKey = ['contest_id', 'rating'];

    static fields () {
        return {
            contest_id: this.attr(null),
            rating: this.attr(null),
            reward: this.attr(null),
            transaction: this.attr(null),
        }
    }
}
