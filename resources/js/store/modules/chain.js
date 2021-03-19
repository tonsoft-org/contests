import {CHAIN_SET_STATE} from '../actions.type'

const state = () => ({
    state: null,
});

const getters = {
    getChainState: (state) => { return state.state; }
};

const actions = {
    [CHAIN_SET_STATE]: ({commit}, data) => {
        localStorage.setItem("chain.state", data);
        commit(CHAIN_SET_STATE, data);
    }
};

const mutations = {
    [CHAIN_SET_STATE]: (state, data) => {
        state.state = data;
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
}
