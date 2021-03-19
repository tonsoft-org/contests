import Vue from 'vue'
import Vuex from 'vuex'
import VuexORM from '@vuex-orm/core'
import Contest from "../models/contest";
import chain from "./modules/chain";
import contest from "./modules/contests";
import Jury from "../models/jury";
import ContestJury from "../models/contest_jury";
import Contender from "../models/contender";
import ContenderContest from "../models/contender_contest";
import Vote from "../models/vote";
import Submission from "../models/submission";
import ContestReward from "../models/contest_reward";

Vue.use(Vuex);

const database = new VuexORM.Database();
database.register(Contest);
database.register(Jury);
database.register(ContestJury);
database.register(Contender);
database.register(ContenderContest);
database.register(Vote);
database.register(Submission);
database.register(ContestReward);

export function createStore () {
    return new Vuex.Store({
        plugins: [VuexORM.install(database)],
        modules: {
            chain,
            contest,
        }
    })
}
