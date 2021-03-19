import { Model } from '@vuex-orm/core'
import ContestJury from "./contest_jury";
import Contest from "./contest";
import Vote from "./vote";

export default class Jury extends Model {
    static entity = 'juries';
    static primaryKey = 'id';

    static fields () {
        return {
            id: this.attr(null),

            contests: this.belongsToMany(Contest, ContestJury, 'jury_id', 'contest_id'),
            votes: this.hasMany(Vote, 'jury_id', 'id'),
        }
    }
}
