import Vue from "vue"
import Router from "vue-router"

import NotFound from "../views/layouts/NotFound"
import MainIndex from "../views/Main/MainIndex";
import ContestsIndex from "../views/Contests/ContestsIndex";
import ContestsShow from "../views/Contests/ContestsShow";
import ContendersIndex from "../views/Contenders/ContendersIndex";
import JuriesIndex from "../views/Juries/JuriesIndex";

Vue.use(Router);

export function createRouter() {
    return new Router({
        mode: 'history',
        routes: [
            {
                path: "/contests",
                name: "contests.index",
                component: ContestsIndex,
            },
            {
                path: "/contests/:id",
                name: "contests.show",
                component: ContestsShow,
                props: true
            },
            {
                path: "/contenders",
                name: "contenders.index",
                component: ContendersIndex,
            },
            {
                path: "/juries",
                name: "juries.index",
                component: JuriesIndex,
            },
            {
                path: "/",
                name: "main.index",
                component: MainIndex,
            },
            {
                path: "*",
                name: "not-found",
                component: NotFound,
            },
        ]
    });
}
