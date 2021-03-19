import {
    CONTESTS_TOTAL_COUNT,
} from '../actions.type'

const state = () => ({
    total_count: 0,
});

const getters = {
    getContestsTotalCount: (state) => { return state.total_count; },
};

const actions = {
    [CONTESTS_TOTAL_COUNT]({commit}){
        this._vm.$api.getContestsCount().then((result) => {
            commit(CONTESTS_TOTAL_COUNT, result);
        });
    },
};

const mutations = {
    [CONTESTS_TOTAL_COUNT]: (state, data) => {
        state.total_count = data;
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
}
