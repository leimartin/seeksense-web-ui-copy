<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<div v-if="report.length > 0">
    <div v-for="R in report" :key="R.id">
        <section class="card-header my-4 py-1">
            <p class="card-header-title">{{ R.username }} @ {{ R.ip }} [{{ R.datetime }}]</p>

            <!--<figure class="recent_img">
                <img :src="H.query" alt="Query Image">
            </figure>

            <span class="ml-2" style="border-right: 3px solid #ccc;"></span>
            <div v-for="image in H.payload.documents.slice(0, 3)">
                <figure class="recent_img mx-1">
                    <img :src="image" alt="Matched Image">
                </figure>
            </div>-->
        </section>
    </div>
</div>

