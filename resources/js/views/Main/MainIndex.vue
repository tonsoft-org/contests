<template>
    <base-layout>
        <template v-slot:content-section>
            <v-col cols="12" sm="10">
                <v-row>
                    <v-col cols="12">
                        <v-card elevation="1" class="text-center pa-5">
                            <div class="text-h3 light-blue--text text--darken-3">{{ getContestsCount }} / {{ getContestsTotalCount }}</div>
                            <div class="caption">{{ $t('Contests loaded') }}</div>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="12" sm="4">
                        <v-card elevation="1" class="mt-4 text-center pa-5">
                            <div>
                                <div class="text-h3 light-blue--text text--darken-3">{{ getContestsCount }}</div>
                                <div class="caption">{{ $t('Contests') }}</div>
                            </div>
                        </v-card>
                    </v-col>

                    <v-col cols="12" sm="4">
                        <v-card elevation="1" class="mt-4 text-center pa-5">
                            <div>
                                <div class="text-h3 light-blue--text text--darken-3">{{ getContendersCount }}</div>
                                <div class="caption">{{ $t('Contenders') }}</div>
                            </div>
                        </v-card>
                    </v-col>

                    <v-col cols="12" sm="4">
                        <v-card elevation="1" class="mt-4 text-center pa-5">
                            <div>
                                <div class="text-h3 light-blue--text text--darken-3">{{ getJuriesCount }}</div>
                                <div class="caption">{{ $t('Juries') }}</div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </template>

        <template v-slot:r-sidebar-section>
            <div></div>
        </template>
    </base-layout>
</template>

<script>
import BaseLayout from "../layouts/BaseLayout";
import Contest from "../../models/contest";
import Jury from "../../models/jury";
import Contender from "../../models/contender";
import {mapGetters} from "vuex";

export default {
    components: {BaseLayout},
    computed: {
        ...mapGetters(["getChainState", "getContestsTotalCount"]),

        getContestsCount () {
            return Contest.query().count();
        },

        getContendersCount () {
            return Contender.query().count();
        },

        getJuriesCount () {
            return Jury.query().count();
        },
    }
}
</script>
