export default class Client {
    async getContestsCount () {
        return await axios.get('/api/contests/count').then(({data}) => data.data);
    }

    async getContestsByChunk (
        chunk = 10,
          {
              id = {ne: ''},
              code_hash = {eq: "5daea8b855140d110ab07d430883bfecdd4cba9bcded8968fae7fa6cdb5adfbd"},
              last_paid = {gt: 0},
          } = {}
      ) {
        let params = {
            with: ['contenders', 'juries', 'submissions', 'votes', 'contest_rewards'],
            limit: chunk,
        };
        id = (id.hasOwnProperty('eq')) ? {id: id.eq} : {};
        last_paid = (last_paid.hasOwnProperty('lt')) ? {last_paid_lt: last_paid.lt} : {last_paid_gt: last_paid.gt};

        let contests = await axios.get('/api/contests', {
            params: {...params, ...id, ...last_paid}
        });

        return contests['data']['data'];
    }
}
