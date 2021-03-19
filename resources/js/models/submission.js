import { Model } from '@vuex-orm/core'
import Vote from "./vote";
import Contest from "./contest";

export default class Submission extends Model {
    static entity = 'submissions';
    static primaryKey = ['id', 'contest_id'];

    static fields () {
        return {
            id: this.attr(null),
            contest_id: this.attr(null),
            address: this.attr(null),
            contact: this.attr(null),
            applied_at: this.attr(null),
            file_link: this.attr(null),
            hash: this.attr(null),

            total_mark: this.attr(null),
            avg_mark: this.attr(null),
            accepted: this.attr(null),
            abstained: this.attr(null),
            rejected: this.attr(null),

            votes: this.hasMany(Vote, 'submission_id', 'id'),
        }
    }
}
