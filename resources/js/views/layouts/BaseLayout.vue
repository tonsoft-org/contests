<template>
    <v-app id="inspire">
        <v-app-bar
            app
            color="white"
            flat
            elevation="1"
        >
            <v-btn text color="light-blue darken-3" class="pa-4" :to="{name: 'main.index'}" exact>
                <v-icon left>
                    mdi-diamond-stone
                </v-icon>

                Contests.TonSoft.org
            </v-btn>

            <v-tabs
                optional
                centered
                class="ml-n9"
                color="light-blue darken-3"
                v-model="chainState"
            >
                <v-tab :href="'#'+'offchain'">
                    {{ $t('OFF-chain') }}
                </v-tab>
                <v-tab :href="'#'+'onchain'">
                    {{ $t('ON-chain') }}
                </v-tab>
            </v-tabs>

            <v-btn text color="blue-grey darken-2" class="pa-4">
                Anonymous (TonSoft.org)
            </v-btn>
        </v-app-bar>

        <v-main class="grey lighten-3">
            <v-container fluid>
                <v-row>
                    <slot name="l-sidebar-section">
                        <v-col cols="12" sm="2">
                            <slot name="l-sidebar">
                                <v-card
                                    min-height="268"
                                    elevation="1"
                                >
                                    <v-list flat>
                                        <v-list-item-group>
                                            <v-list-item
                                                v-for="(item, i) in lSidebarLinks"
                                                :key="i"
                                                :to="{ name: item.route}"
                                                color="light-blue darken-3"
                                                exact
                                            >
                                                <v-list-item-icon>
                                                    <v-icon v-text="item.icon" />
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$t(item.text)" />
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-card>
                            </slot>
                        </v-col>
                    </slot>

                    <slot name="content-section">
                        <v-col cols="12" sm="8">
                            <slot name="content">
                                <!-- -->
                            </slot>
                        </v-col>
                    </slot>

                    <slot name="r-sidebar-section">
                        <v-col cols="12" sm="2">
                            <slot name="r-sidebar">
                                <v-card elevation="1" class="text-center pa-5">
                                    <div class="text-h3 light-blue--text text--darken-3">{{ getContestsCount }} / {{ getContestsTotalCount }}</div>
                                    <div class="caption">{{ $t('Contests loaded') }}</div>
                                </v-card>

                                <v-card elevation="1" class="mt-4 text-center pa-5">
                                    <div>
                                        <div class="text-h3 light-blue--text text--darken-3">{{ getContestsCount }}</div>
                                        <div class="caption">{{ $t('Contests') }}</div>
                                    </div>

                                    <div class="mt-5">
                                        <div class="text-h3 light-blue--text text--darken-3">{{ getContendersCount }}</div>
                                        <div class="caption">{{ $t('Contenders') }}</div>
                                    </div>

                                    <div class="mt-5">
                                        <div class="text-h3 light-blue--text text--darken-3">{{ getJuriesCount }}</div>
                                        <div class="caption">{{ $t('Juries') }}</div>
                                    </div>
                                </v-card>

                                <v-card v-if="getContendersReward" elevation="1" class="mt-4 text-center pa-5">
                                    <div>
                                        <div class="text-h3 light-blue--text text--darken-3">{{ getContendersReward }}</div>
                                        <div class="caption">{{ $t('Contenders reward') }}</div>
                                    </div>
                                </v-card>

                                <v-card v-if="getJuriesReward" elevation="1" class="mt-4 text-center pa-5">
                                    <div>
                                        <div class="text-h3 light-blue--text text--darken-3">{{ getJuriesReward }}</div>
                                        <div class="caption">{{ $t('Juries reward') }}</div>
                                    </div>
                                </v-card>
                            </slot>
                        </v-col>
                    </slot>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>

import {mapGetters} from "vuex";
import Contest from "../../models/contest";
import Contender from "../../models/contender";
import Jury from "../../models/jury";

export default {
    name: "BaseLayout",
    data: () => ({
        showSnackbar: true,
        lSidebarLinks: [
            {
                text: 'Home',
                icon: 'mdi-home',
                route: 'main.index',
            },
            {
                text: 'Contests',
                icon: 'mdi-file-document-multiple',
                route: 'contests.index',
            },
            {
                text: 'Contenders',
                icon: 'mdi-account-supervisor',
                route: 'contenders.index',
            },
            {
                text: 'Juries',
                icon: 'mdi-bank',
                route: 'juries.index',
            },
        ],
    }),
    created() {
        this.$store.dispatch('CONTESTS_TOTAL_COUNT');
    },
    computed: {
        ...mapGetters(["getChainState", "getContestsTotalCount"]),
        chainState: {
            get(){
                return this.getChainState;
            },
            set(value){
                this.$store.dispatch('CHAIN_SET_STATE', value);
                location.reload();
            }
        },

        getContestsCount () {
            return Contest.query().count();
        },

        getContendersCount () {
            return Contender.query().count();
        },

        getJuriesCount () {
            return Jury.query().count();
        },

        getContendersReward () {
            return Contest.query().sum('contenders_reward');
        },

        getJuriesReward () {
            return Contest.query().sum('juries_reward');
        },
    }
};
</script>

<style>
.v-data-table__expanded.v-data-table__expanded__content {
    box-shadow: none !important;
}
</style>
