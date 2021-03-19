<template>
    <base-layout>
        <template v-slot:content>
            <v-container fluid>
                <v-row>
                    <v-col>
                        <template v-if="getContenders.length > 0">
                            <v-card>
                                <v-card-title>
                                    <v-text-field
                                        v-model="search"
                                        append-icon="mdi-magnify"
                                        :label="$t('Search')"
                                        single-line
                                        hide-details
                                    ></v-text-field>
                                </v-card-title>

                                <v-data-table
                                    :headers="[{text: 'ID', value: 'id',}, {text: 'Contests', value: 'contests',}]"
                                    :items="getContenders"
                                    :search="search"
                                    :single-expand="true"
                                    item-key="id"
                                    show-expand
                                    class="elevation-1"
                                    sort-by="contests"
                                    :sort-desc="true"
                                    :items-per-page="15"
                                    @click:row="(item, slot) => slot.expand(!slot.isExpanded)"
                                >
                                    <template v-slot:expanded-item="{ headers, item }">
                                        <td :colspan="headers.length" class="td-blue-border">
                                            <v-list dense>
                                                <v-subheader>{{ $t('Contests') }}</v-subheader>
                                                <v-list-item-group color="light-blue darken-3">
                                                    <v-list-item
                                                        v-for="(contest, i) in item.contests"
                                                        :key="i"
                                                        :to="{name:'contests.show', params: {id: contest.id}}"
                                                    >
                                                        <v-list-item-content>
                                                            <v-list-item-title v-text="contest.title"></v-list-item-title>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                </v-list-item-group>
                                            </v-list>
                                        </td>
                                    </template>
                                    <template v-slot:item.contests="{item}">
                                        {{ item.contests.length }}
                                    </template>
                                </v-data-table>
                            </v-card>
                        </template>
                        <template v-else>
                            <div class="text-center">
                                <v-progress-circular
                                    :size="50"
                                    color="primary"
                                    indeterminate
                                ></v-progress-circular>
                            </div>
                        </template>
                    </v-col>
                </v-row>
            </v-container>
        </template>
    </base-layout>
</template>

<script>
import BaseLayout from "../layouts/BaseLayout";
import Contender from "../../models/contender";

export default {
    components: {BaseLayout},
    data: function () {
        return {
            search: '',
        };
    },
    computed: {
        getContenders: function () {
            return Contender.query().with(['contests']).get();
        }
    }
}
</script>

<style scoped>
    .td-blue-border {
        border-left: 2px solid #0277bd;
    }
</style>
