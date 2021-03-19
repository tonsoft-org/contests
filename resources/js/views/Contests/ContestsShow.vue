<template>
    <base-layout>
        <template v-slot:content>
            <template v-if="getContest">
                <v-alert
                    border="right"
                    colored-border
                    type="info"
                    elevation="2"
                    color="light-blue darken-3"
                    v-if="contest_rewards.length === 0"
                >
                    {{ $t('There are no rewards configured in this contest. You can find them out by following the link on the forum.') }}
                </v-alert>
                <v-alert
                    border="right"
                    colored-border
                    type="warning"
                    elevation="2"
                    v-if="(Number(getContest.voting_end_at)*1000) > date"
                >
                    {{ $t('The contest is not over yet. Results are subject to change.') }}
                </v-alert>

                <v-sheet elevation="1" rounded>
                    <v-card  class="mx-auto full-width" elevation="0">
                        <v-card-title>{{ getContest.title }}</v-card-title>

                        <v-card-text>
                            <div class="caption">{{ getContest.id }}</div>
                            <div class="caption mt-2">
                                <span class="font-weight-bold">{{ $t('Forum link') }}:</span>
                                {{ getContest.link }}
                            </div>
                            <div class="caption mt-2">
                                <span class="font-weight-bold">{{ $t('Contest date:') }}:</span>
                                {{ (new Date(Number(getContest.start_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                -
                                {{  (new Date(Number(getContest.end_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                            </div>
                            <div class="caption mt-2">
                                <span class="font-weight-bold">{{ $t('Voting date') }}:</span>
                                {{  (new Date(Number(getContest.end_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                -
                                {{  (new Date(Number(getContest.voting_end_at)*1000).toLocaleDateString(undefined,  { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                            </div>
                        </v-card-text>
                    </v-card>

                    <v-sheet elevation="0" class="mt-10">
                        <v-tabs v-model="tab"
                                centered
                                fixed-tabs
                                background-color="light-blue darken-3"
                                dark
                        >
                            <v-tab>
                                {{ $t('Results') }}
                            </v-tab>
                            <v-tab>
                                {{ $t('Submissions') }} ({{ getContest.submissions.length }})
                            </v-tab>
                            <v-tab>
                                {{ $t('Juries') }} ({{ getContest.juries.length }})
                            </v-tab>
                        </v-tabs>
                        <v-tabs-items v-model="tab" class="mt-1">
                            <v-tab-item>
                                <template v-if="getContestResult">
                                    <v-data-table
                                        :headers="[
                                                  { text: $t('Rating'), value: 'rating', align: 'center'},
                                                  { text: $t('Contender'), value: 'contender_id' },
                                                  { text: $t('ID'), value: 'submission_id', align: 'center'},
                                                  { text: $t('Avg. mark'), value: 'avg_mark', align: 'center' },
                                                  { text: $t('Accepted (%)'), value: 'accepted_percent', align: 'center' },
                                                  { text: $t('Accepted'), value: 'accepted', align: 'center' },
                                                  { text: $t('Abstained'), value: 'abstained', align: 'center' },
                                                  { text: $t('Rejected'), value: 'rejected', align: 'center' },
                                                  { text: $t('Reward'), value: 'reward', align: 'center' },
                                                ]"
                                        :items="getContestResult"
                                        class="elevation-0 pa-5"
                                        disable-pagination
                                        hide-default-footer
                                    >
                                        <template v-slot:top>
                                            <v-row>
                                                <v-col md="6">
                                                    <v-switch
                                                        v-model="contest_logic_type"
                                                        :label="$t('Yes/No logic')"
                                                        class="pa-3"
                                                    ></v-switch>
                                                </v-col>
                                                <v-col md="6" class="text-right">
                                                    <v-dialog
                                                        transition="dialog-top-transition"
                                                        max-width="600"
                                                    >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-btn
                                                                color="light-blue darken-3" dark class="mt-5" elevation="0"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            >{{ $t('Reward settings') }}</v-btn>
                                                        </template>
                                                        <template v-slot:default="dialog">
                                                            <v-card>
                                                                <v-toolbar color="light-blue darken-3" dark>
                                                                    <v-btn
                                                                        icon
                                                                        dark
                                                                        @click="dialog.value = false"
                                                                    >
                                                                        <v-icon>mdi-close</v-icon>
                                                                    </v-btn>
                                                                    <v-toolbar-title>{{ $t('Reward settings') }}</v-toolbar-title>
                                                                </v-toolbar>
                                                                <v-card-text>
                                                                    <v-data-table
                                                                        :headers="[
                                                                          { text: $t('Rating'), value: 'index', align: 'center'},
                                                                          { text: $t('Reward'), value: 'reward', align: 'center'},
                                                                        ]"
                                                                        :items="contest_rewards"
                                                                        disable-pagination
                                                                        hide-default-footer
                                                                    >
                                                                        <template #item="{ item, index, headers }">
                                                                            <tr>
                                                                                <td v-for="n in headers" class="text-center">
                                                                                    <template v-if="n.value === 'index'">
                                                                                        {{ contest_rewards[index].rating }}
                                                                                    </template>
                                                                                    <template v-else-if="n.value === 'reward'">
                                                                                        <v-text-field
                                                                                            v-model="contest_rewards[index].reward"
                                                                                        ></v-text-field>
                                                                                    </template>
                                                                                </td>
                                                                            </tr>
                                                                        </template>
                                                                    </v-data-table>
                                                                </v-card-text>
                                                                <v-card-actions class="justify-center">
                                                                    <v-btn
                                                                        text
                                                                        @click="contest_rewards.push({
                                                                            rating: (contest_rewards.length)+1,
                                                                            reward: 0,
                                                                        })"
                                                                    >
                                                                        {{ $t('Add reward') }}
                                                                    </v-btn>
                                                                    <v-btn text @click="contest_rewards.pop()">
                                                                        {{ $t('Delete reward') }}
                                                                    </v-btn>
                                                                    <v-btn text @click="dialog.value = false">
                                                                        {{ $t('Save') }}
                                                                    </v-btn>
                                                                </v-card-actions>
                                                            </v-card>
                                                        </template>
                                                    </v-dialog>


                                                    <v-dialog
                                                        transition="dialog-top-transition"
                                                        max-width="600"
                                                    >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-btn
                                                                color="light-blue darken-3" dark class="mt-5" elevation="0"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            >{{ $t('Jury reward settings') }}</v-btn>
                                                        </template>
                                                        <template v-slot:default="dialog">
                                                            <v-card>
                                                                <v-toolbar color="light-blue darken-3" dark>
                                                                    <v-btn
                                                                        icon
                                                                        dark
                                                                        @click="dialog.value = false"
                                                                    >
                                                                        <v-icon>mdi-close</v-icon>
                                                                    </v-btn>
                                                                    <v-toolbar-title>{{ $t('Jury reward settings') }}</v-toolbar-title>
                                                                </v-toolbar>
                                                                <v-card-text>
                                                                    <v-text-field v-model="juries_reward_percent" label="Jury reward %"/>                                                                    </v-text-field>
                                                                </v-card-text>
                                                                <v-card-actions class="justify-center">
                                                                    <v-btn text @click="dialog.value = false">
                                                                        {{ $t('Save') }}
                                                                    </v-btn>
                                                                </v-card-actions>
                                                            </v-card>
                                                        </template>
                                                    </v-dialog>
                                                </v-col>
                                            </v-row>
                                        </template>

                                        <template v-slot:item.contender_id="{ item }">
                                            {{ item.contender_id.substring(0, 5) }}
                                            ...
                                            {{ item.contender_id.substring(61) }}
                                        </template>

                                        <template v-slot:item.accepted_percent="{ item }">
                                            {{ item.accepted_percent }}
                                        </template>
                                    </v-data-table>
                                    <v-row class="text-right">
                                        <v-col class="pr-10">
                                            <v-btn
                                                @click="exportToExcel"
                                                color="light-blue darken-3"
                                                dark
                                                class="mt-3"
                                                elevation="0"
                                            >
                                                {{ $t('Export to excel') }}
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                    <v-row class="text-right">
                                        <v-col class="pr-10">
                                            <v-btn
                                                @click="exportContenderReward"
                                                color="light-blue darken-3"
                                                dark
                                                elevation="0"
                                            >
                                                {{ $t('Export contender/reward') }}
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                    <v-row class="text-right">
                                        <v-col class="pr-10 pb-10">
                                            <v-btn
                                                @click="exportJuryReward"
                                                color="light-blue darken-3"
                                                dark
                                                elevation="0"
                                            >
                                                {{ $t('Export jury/reward') }}
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </template>
                            </v-tab-item>
                            <v-tab-item>
                                <v-data-table
                                    :headers="[{ text: $t('ID'), value: 'address'}]"
                                    :items="getContest.submissions"
                                    :items-per-page="15"
                                    class="elevation-0 pa-5"
                                    :single-expand="true"
                                    item-key="id"
                                    show-expand
                                    @click:row="(item, slot) => slot.expand(!slot.isExpanded)"
                                >
                                    <template v-slot:expanded-item="{headers, item}">
                                        <td :colspan="headers.length" class="td-blue-border">
                                            <div class="caption mt-2">
                                                <span class="font-weight-bold">{{ $t('Applied at') }}:</span>
                                                {{ (new Date(Number(item.applied_at)*1000).toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })) }}
                                            </div>
                                            <div class="caption mt-2">
                                                <span class="font-weight-bold">{{ $t('File link') }}:</span>
                                                {{ item.file_link }}
                                            </div>

                                            <v-data-table
                                                :headers="[
                                                  { text: $t('Comment'), value: 'comment' },
                                                  { text: $t('Type'), value: 'type_text' },
                                                  { text: $t('Mark'), value: 'mark' },
                                                  { text: $t('Jury'), value: 'jury_id' },
                                                ]"
                                                :items="item.votes"
                                                :items-per-page="15"
                                                class="elevation-0 mt-3"
                                            />
                                        </td>
                                    </template>
                                </v-data-table>
                            </v-tab-item>
                            <v-tab-item>
                                <v-data-table
                                    :headers="[{ text: $t('ID'), value: 'id'}]"
                                    :items="getContest.juries"
                                    :items-per-page="15"
                                    class="elevation-0 pa-5"
                                    :single-expand="true"
                                    item-key="id"
                                    show-expand
                                    @click:row="(item, slot) => slot.expand(!slot.isExpanded)"
                                >
                                    <template v-slot:expanded-item="{headers, item}">
                                        <td :colspan="headers.length" class="td-blue-border">
                                            <v-data-table
                                                :headers="[
                                                  { text: $t('Comment'), value: 'comment' },
                                                  { text: $t('Type'), value: 'type_text' },
                                                  { text: $t('Mark'), value: 'mark' },
                                                  { text: $t('Submission'), value: 'submission_id' },
                                                ]"
                                                :items="item.votes"
                                                :items-per-page="15"
                                                class="elevation-0"
                                            ></v-data-table>
                                        </td>
                                    </template>
                                </v-data-table>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-sheet>
                </v-sheet>
            </template>
            <div v-else class="text-center pa-5">
                <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                ></v-progress-circular>
            </div>
        </template>
    </base-layout>
</template>

<script>
import BaseLayout from "../layouts/BaseLayout";
import Contest from "../../models/contest";
import Vote from "../../models/vote";
import Rating from "../../api/Rating";
import Saver from "../../api/Saver";

export default {
    components: {BaseLayout},
    props: {
        id: String,
    },
    data: function () {
        return {
            tab: 0,
            contest_logic_type: 0,
            contest_rewards: [],
            juries_reward_percent: 0,
            rating: null,
            date: null,
        }
    },
    mounted() {
        this.date = Date.now();
        this.$api.getContestsByChunk(1, {id: {eq: this.id}}).then((contests_part) => {
            Contest.insert({ data: contests_part});
            let contest = contests_part[0];

            this.contest_rewards = contest.contest_rewards ?? [];
            this.juries_reward_percent = contest.juries_reward_percent ?? 0;

            this.rating = new Rating(this.id);
            for (let index in contest.submissions) {
                this.rating.addSubmission(
                    contest.submissions[index].id,
                    contest.submissions[index].address,
                    contest.submissions[index].total_mark,
                    contest.submissions[index].avg_mark,
                    contest.submissions[index].accepted,
                    contest.submissions[index].abstained,
                    contest.submissions[index].rejected,
                );
            }
        });
    },
    computed: {
        getVotes: function () {
            return Vote.all();
        },
        getContest: function () {
            return Contest
                .query()
                .where('id', this.id)
                .with('submissions.votes' , (query) => {
                    query.where('contest_id', this.id);
                })
                .with('juries.votes', (query) => {
                    query.where('contest_id', this.id);
                })
                .first();
        },
        getContestResult: function () {
            if (this.rating) {
                return this.rating.getResult(Number(this.contest_logic_type), this.contest_rewards);
            }
        },
    },
    methods: {
        exportToExcel () {
            Saver.exportToExcel(
                this.getContest.title,
                this.getContestResult,
                ["Rating", "ID", "Address", "Total mark", "Avg. mark", "Accepted (Percent)", "Accepted", "Abstained", "Rejected", "Reward"]
            );
        },
        exportContenderReward () {
            Saver.exportToExcel(
                this.getContest.title,
                this.getContestResult.map((result) => { return [result['contender_id'], result['reward']]}),
                ["Address", "Reward"]
            );
        },
        exportJuryReward () {
            Saver.exportToExcel(
                this.getContest.title,
                this.rating.getJuryResult(this.getContest.juries, this.juries_reward_percent, this.contest_rewards)
                    .map((result) => { return [result['jury_id'], result['reward']]}),
                ["Address", "Reward"]
            );
        }
    }
}
</script>

<style scoped>
.td-blue-border {
    border-left: 2px solid #0277bd;
}
</style>
