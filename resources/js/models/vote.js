import { Model } from '@vuex-orm/core'

export default class Vote extends Model {
    static entity = 'votes';
    static primaryKey = ['contest_id', 'submission_id', 'jury_id'];

    static fields () {
        return {
            contest_id: this.attr(null),
            submission_id: this.attr(null),
            jury_id: this.attr(null),

            type: this.attr(null),
            type_text: this.attr(null),
            mark: this.attr(null),
            comment: this.attr(null),
        }
    }
}
