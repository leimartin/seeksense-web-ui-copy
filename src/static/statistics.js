const stats = Vue.createApp({
    data() {
        return {
            stats: [],
        };
    },
    created() {
        this.fetchStatistics();
    },
    methods: {
        async fetchStatistics() {
            const response = await axios.get(`../admin/records/statistics.php`);
            this.stats = response.data;
            console.log(this.stats);

            this.faceStats = (await axios.get("/api/v1/face/stats")).data
            console.log(this.faceStats);

        }
    }

});

stats.mount("#statistics");