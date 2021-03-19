import { Model } from '@vuex-orm/core'
import Vote from "./vote";

export default class ContenderContest extends Model {
    static entity = 'contenders_contests';
    static primaryKey = ['contender_id', 'contest_id'];

    static fields () {
        return {
            contender_id: this.attr(null),
            contest_id: this.attr(null),
        }
    }
}
