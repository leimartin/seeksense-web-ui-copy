const searchItem = Vue.createApp({
        data() {
            return {
                items: [],
                selectedItem: null,
                result_images: [],
                result_metadatas: [],
                distances: [],
                error_message: ''
            }
        },
        async created() {
            setTimeout(() => initDropZone(document.getElementById('droppr'), this.handleFile), 50);
            this.userSearches();
        },
        methods: {
            async userSearches() {
                this.items = [];
                let lastId = 0x7fffffff;
                while (lastId !== undefined) {
                    const response = await axios.get(`../search_item.php?lastId=${lastId}`)
                    if (response.data.error) {
                        alert(response.data.error);
                        return;
                    }
                    const row = response.data;
                    this.items.push(row);
                    if (row.id < lastId) {
                        lastId = row.id;
                    } else break;
                    console.log(response.data)
                }
            },
            showContent(item) {
                this.selectedItem = item;
                this.result_images = item.payload.documents || [];
                this.result_metadatas = item.payload.metadatas || [];
                this.distances = item.payload.distances || [];
                this.error_message = '';
                if (item.payload.detail) {
                    this.error_message = item.payload.detail;
                }
                console.log(this.distances);
            },
            close(item) {
                this.selectedItem = null;
            },
            async handleFile(filename, fileb64) {
                this.selectedItem = {query: fileb64, query_filename: filename}
                const formData = new FormData();
                formData.append('file', await url2file(filename, fileb64), filename);
                const response = await axios.post('/seeksense.php', formData);
                // todo: file upload success message
                this.result_images = response.data.documents || [];
                this.result_metadatas = response.data.metadatas || [];
                this.distances = response.data.distances || [];
                this.error_message = '';
                if (response.data.detail) {
                    this.error_message = response.data.detail;
                }
            },
        }
    })
;

searchItem.mount('#searchHistory');