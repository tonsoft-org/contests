import { Model } from '@vuex-orm/core'

export default class ContestJury extends Model {
    static entity = 'contests_juries';
    static primaryKey = ['contest_id', 'jury_id'];

    static fields () {
        return {
            contest_id: this.attr(null),
            jury_id: this.attr(null),
        }
    }
}
