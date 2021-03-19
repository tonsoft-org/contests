<template>
    <base-layout>
        <template v-slot:content>
            <v-container fluid>
                <v-row>
                    <template v-if="getContestsTotalCount > 0">
                        <v-col cols="12">
                            <v-card class="mx-auto full-width" elevation="1">
                                <v-row class="pl-5 pr-5">
                                    <v-col cols="12" md="6" class="mt-2">
                                        <v-text-field
                                            v-model="filter.contest_id"
                                            :label="$t('Contest ID')"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select
                                            class="pr-md-5 pl-md-5"
                                            v-model="filter.selected_statuses"
                                            :items="statuses"
                                            :label="$t('Statuses')"
                                            multiple
                                            chips
                                            persistent-hint
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-col>

                        <v-col v-for="contest in getContests" :key="contest.id" cols="12" md="6">
                            <v-card class="mx-auto full-width" elevation="1">
                                <v-card-title>{{ contest.title }}</v-card-title>

                                <v-card-text>
                                    <div class="caption">{{ contest.id }}</div>
                                    <div class="caption mt-2">
                                        <span class="font-weight-bold">{{ $t('Forum link') }}:</span>
                                        <a :href="contest.link" target="_blank">{{ contest.link }}</a>
                                    </div>
                                    <div class="caption mt-2">{{ contest.status }}</div>
                                    <div class="caption mt-2">
                                        <span class="font-weight-bold">{{ $t('Contest date:') }}:</span>
                                        {{ (new Date(Number(contest.start_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                        -
                                        {{  (new Date(Number(contest.end_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                    </div>
                                    <div class="caption mt-2">
                                        <span class="font-weight-bold">{{ $t('Voting date') }}:</span>
                                        {{  (new Date(Number(contest.end_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                        -
                                        {{  (new Date(Number(contest.voting_end_at)*1000).toLocaleDateString(undefined,  { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                    </div>
                                </v-card-text>

                                <v-card-text>
                                    <v-chip
                                        class="ma-2"
                                        color="light-blue darken-3"
                                        text-color="white"
                                    >
                                        <v-avatar
                                            left
                                            class="light-blue darken-4"
                                        >
                                            {{ contest.contenders.length }}
                                        </v-avatar>
                                        {{ $t('Contenders') }}
                                    </v-chip>

                                    <v-chip
                                        class="ma-2"
                                        color="indigo"
                                        text-color="white"
                                    >
                                        <v-avatar
                                            left
                                            class="indigo darken-4"
                                        >
                                            {{ contest.juries.length }}
                                        </v-avatar>
                                        {{ $t('Juries') }}
                                    </v-chip>

                                    <template>
                                        <v-chip
                                            class="ma-2"
                                            color="blue-grey"
                                            text-color="white"
                                            v-if="(date > contest.voting_end_at)"
                                        >
                                            <v-avatar left>
                                                <v-icon>mdi-close-circle</v-icon>
                                            </v-avatar>
                                            {{ $t('Close') }}
                                        </v-chip>
                                        <v-chip
                                            class="ma-2"
                                            color="deep-orange"
                                            text-color="white"
                                            v-else-if="(date > contest.end_at)"
                                        >
                                            <v-avatar left>
                                                <v-icon>mdi-clock</v-icon>
                                            </v-avatar>
                                            {{ $t('Voting') }}
                                        </v-chip>
                                        <v-chip
                                            class="ma-2"
                                            color="teal"
                                            text-color="white"
                                            v-else-if="(date >= contest.start_at)"
                                        >
                                            <v-avatar left>
                                                <v-icon>mdi-checkbox-marked-circle</v-icon>
                                            </v-avatar>
                                            {{ $t('Open') }}
                                        </v-chip>
                                        <v-chip
                                            class="ma-2"
                                            color="light-blue"
                                            text-color="white"
                                            v-else-if="(date < contest.start_at)"
                                        >
                                            <v-avatar left>
                                                <v-icon>mdi-calendar-clock</v-icon>
                                            </v-avatar>
                                            {{ $t('Soon') }}
                                        </v-chip>
                                    </template>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="light-blue darken-3"
                                        text
                                        :to="{name: 'contests.show', params: {id: contest.id} }"
                                    >
                                        {{ $t('Read more') }}
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-col>

                        <v-col cols="12">
                            <div class="text-center">
                                <v-pagination
                                    v-model="page"
                                    :length="Math.ceil((contests_count)/on_page)"
                                ></v-pagination>
                            </div>
                        </v-col>
                    </template>
                    <template v-else>
                        <v-col cols="12" class="text-center pa-5">
                            <v-progress-circular
                                :size="50"
                                color="primary"
                                indeterminate
                            ></v-progress-circular>
                        </v-col>
                    </template>
                </v-row>
            </v-container>
        </template>
    </base-layout>
</template>

<script>
import BaseLayout from "../layouts/BaseLayout";
import Contest from "../../models/contest";
import {mapGetters} from "vuex";

export default {
    components: {BaseLayout},
    data: function () {
        return {
            date: null,
            page: 1,
            on_page: 10,
            contests_count: 1,
            filter: {
                contest_id: '',
                selected_statuses: ['Soon', 'Open', 'Voting', 'Close'],
            },
            statuses: ['Soon', 'Open', 'Voting', 'Close'],
        };
    },
    mounted() {
        this.date = (new Date)/1000;
    },
    computed: {
        ...mapGetters(["getContestsTotalCount"]),
        getContests: function () {
            let contests = Contest
                .query()
                .orderBy('last_paid', 'desc')
                .with(['contenders', 'juries']);

            if (this.filter.contest_id !== null) {
                contests.where((contest) => {
                    return contest.id.includes(this.filter.contest_id)
                });
            }

            contests.where((contest) => {
                let soon = false;
                let open = false;
                let voting = false;
                let close = false;

                if (this.filter.selected_statuses.includes('Soon')) {
                    soon = contest.start_at > this.date;
                }

                if (this.filter.selected_statuses.includes('Open')) {
                    open = (contest.start_at <= this.date) && (contest.end_at > this.date);
                }

                if (this.filter.selected_statuses.includes('Voting')) {
                    voting = (contest.end_at <= this.date) && (contest.voting_end_at > this.date);
                }

                if (this.filter.selected_statuses.includes('Close')) {
                    close = (contest.voting_end_at <= this.date);
                }

                return soon || open || voting || close;
            });

            this.contests_count = contests.count();

            return contests.offset((this.page-1)*this.on_page).limit(this.on_page).get();
        },
    }
}
</script>
