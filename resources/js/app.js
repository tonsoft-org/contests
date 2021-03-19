import {TonClient} from "@tonclient/core";

require('./bootstrap');

import Vue from "vue"
import App from "./App.vue"
import vuetify from './plugins/vuetify'
import i18n from './plugins/i18n';
import OnChainClient from "./api/OnChain/Client";
import OffChainClient from "./api/OffChain/Client";

import {createRouter} from "./router";
const router = createRouter();

import {createStore} from "./store";
import {libWeb} from "@tonclient/lib-web";
const store = createStore();

TonClient.useBinaryLibrary(libWeb);
Vue.prototype.$client = new TonClient({network: { server_address: "main.ton.dev" }});
Vue.prototype.$api = (localStorage.getItem('chain.state') === "onchain") ? new OnChainClient(Vue.prototype.$client) : new OffChainClient();

const app = new Vue({
    el: "#app",
    router,
    store,
    vuetify,
    i18n,
    render: h => h(App)
});
