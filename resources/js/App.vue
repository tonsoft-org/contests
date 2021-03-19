<template>
    <router-view/>
</template>

<script>
import Contest from "./models/contest";
import * as contestsAbi from './api/OnChain/abi/contests.json'
import { abiContract } from '@tonclient/core';

export default {
    created() {
        this.$store.dispatch('CHAIN_SET_STATE', localStorage.getItem('chain.state'));

        this.loadContests();
    },
    methods: {
        async loadContests () {
            let last_paid = {gt: 0};
            while (true) {
                let contests_part = await this.$api.getContestsByChunk(10, {last_paid}) ?? [];
                if (contests_part.length === 0) break;
                await Contest.insert({ data: contests_part});
                last_paid = {lt: contests_part[contests_part.length-1].last_paid};
            }
        },
    }
}
</script>
