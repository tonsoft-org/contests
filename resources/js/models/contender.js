import { Model } from '@vuex-orm/core'
import ContestJury from "./contest_jury";
import Contest from "./contest";
import ContenderContest from "./contender_contest";

export default class Contender extends Model {
    static entity = 'Contenders'
    static primaryKey = 'id';

    static fields () {
        return {
            id: this.attr(null),

            contests: this.belongsToMany(Contest, ContenderContest, 'contender_id', 'contest_id'),
        }
    }
}
