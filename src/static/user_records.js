function censorIPAddress(ipAddress) {
    // Check if the input is an IPv4 address
    const ipv4Regex = /^(\d{1,3}\.){3}\d{1,3}$/;
    if (ipv4Regex.test(ipAddress)) {
        const parts = ipAddress.split('.');
        return parts.slice(0, 2).concat(['xxx', 'xxx']).join('.');
    }

    // Check if the input is an IPv6 address
    const ipv6Regex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:))$/;
    if (ipv6Regex.test(ipAddress)) {
        const parts = ipAddress.split(':');
        return [...parts.slice(0, -2), 'xxxx', 'xxxx'].join(':');
    }

    // If the input is neither IPv4 nor IPv6, return the original input
    return ipAddress;
}

const recent_logs = Vue.createApp({
    data() {
        return {
            user_logs: [],
            username: null,
        }
    },
    created() {
        this.displayUserLogs();
    },
    methods: {
        async displayUserLogs() {
            const response = await axios.get('../admin/records/recent_logs.php');
            this.user_logs = response.data.map(_ => {
                _.ip = censorIPAddress(_.ip)
                return _
            })
            // console.log(this.user_logs);
        }
    }
})
recent_logs.mount("#recentLogs");

const history_logs = Vue.createApp({
    data() {
        return {
            history: []
        };
    },
    created() {
        this.displayHistory();
    },
    methods: {
        containsKey(obj, key ) {
            return Object.keys(obj).includes(key);
        },
        async displayHistory() {
            let lastId = 0x7fffffff;
            while (lastId !== undefined) {
                const response = await axios.get(`../admin/records/users_history.php?lastId=${lastId}`);
                if (!response.data || !response.data.id) {
                    return;
                }
                const row = response.data;
                this.history.push(row);
                if (row.id < lastId) {
                    lastId = row.id;
                } else break;
                // console.log(response.data)
            }
        }
    }
});
history_logs.mount('#historyLogs');
