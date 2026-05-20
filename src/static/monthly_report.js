const reportApp = Vue.createApp({
    data() {
        return {
            report: []
        };
    },
    async mounted() {
        await this.fetchData();
    },
    methods: {
        async fetchData() {
            const response = await axios.get('../admin/records/monthly_report.php');
            this.report = response.data;
            console.log(this.report);
        },
        async generate_report() {
            const date = new Date();
            const month = date.toLocaleString('default', {month: 'long'});
            let total_searches = 0;
            let total_logs = 0;

            for(let i = 0 ; i <= this.report.length; i++) {
                total_logs = i;
            }
            this.report.forEach(item => {
                total_searches += item.searches;
            })

            const {jsPDF} = window.jspdf;
            const doc = new jsPDF();

            const generatedAt = `Run Date: ${date.toLocaleString()}\nUser: ${this.report[0].admin || 'N/A'}`;
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(0);
            doc.text(generatedAt, 15, 15);

            const title = `SeekSense Monthly Report`;
            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(7, 25, 82);
            doc.text(title, (doc.internal.pageSize.getWidth() - (doc.getTextWidth(title))) / 2, 32);

            const intro = `The following report details logging and search activity for the month of ${month}. The data, presented in the table below, reflects a total of ${total_logs} logs and ${total_searches} searches.`;
            doc.setFontSize(11.5);
            doc.setFont('helvetica', 'normal');
            const maxWidth = 180;
            doc.setTextColor(0);
            doc.text(intro, 15, 42, { maxWidth });

            const headers = [['IP', 'Date/Time', 'Username', 'Searches']];

            const data = this.report.map(item => [
                item.ip || 'N/A',
                item.datetime,
                item.username,
                item.searches,
            ]);

            const table_style = {
                startY: 53,
                headStyles: {
                    fillColor: [7, 25, 82],
                    textColor: 255,
                    fontSize: 12,
                    halign: 'center',
                },
                bodyStyles: {
                    fontSize: 10,
                    textColor: 0
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245],
                },
                margin: {top: 20},
                columnStyles: {
                    0: {cellWidth: 42}, // IP
                    1: {cellWidth: 54}, // Date/Time
                    2: {cellWidth: 46}, // Username
                    3: {cellWidth: 37.5, halign: 'center'}, // Searches Count
                },
            };

            doc.autoTable({
                head: headers,
                body: data,
                ...table_style,
            });

            const total_pages = doc.internal.getNumberOfPages();
            for (let i = 1; i <= total_pages; i++) {
                const reminder = 'This report is intended for PNP officials to address a potential threat.'
                doc.setFontSize(8);
                const textWidth = doc.getStringUnitWidth(reminder) * doc.internal.getFontSize();
                doc.text(reminder, (doc.internal.pageSize.getWidth() - (textWidth)) + 45, doc.internal.pageSize.getHeight() - 10);

                doc.setPage(i);
                doc.text(`Page ${i} of ${total_pages}`, doc.internal.pageSize.getWidth() - textWidth + 210 ,  doc.internal.pageSize.getHeight() - 10);
            }

            doc.save(`Monthly_Report [${month}].pdf`);
        },
    }
});

reportApp.mount('#generateReport');


