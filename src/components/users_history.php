<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<div  id="historyLogs" style="height:66vh; overflow: auto">
    <div v-if="history.length > 0">
        <div v-for="H in history" :key="H.ids">
            <section class="card-header my-4 py-1">
                <p class="card-header-title">{{ H.username }} @ {{ H.datetime }}</p>

                <figure class="recent_img">
                    <img :src="H.query" alt="Query Image">
                </figure>

                <span class="ml-2" style="border-right: 3px solid #ccc;"></span>
                <div v-if="containsKey(H.payload, 'documents')" v-for="image in H.payload.documents.slice(0, 3)">
                    <figure class="recent_img mx-1">
                        <img :src="image" alt="Matched Image">
                    </figure>
                </div>
                <p v-else style="margin: 16px 16px 0 16px">No faces found in image.</p>
            </section>
        </div>
    </div>
    <div v-else>
        <p>No history found.</p>
    </div>
</div>
