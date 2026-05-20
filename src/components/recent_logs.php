<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<div id="recentLogs" class="py-3" style="height: 43vh; overflow: auto;">
    <div v-if="user_logs.length > 0">
        <div v-for="log in user_logs" :key="log.id">
            <header class="card-header">
                <p class="card-header-title"> {{ log.username }} @ {{ log.ip }} [{{ log.time }}]</p>
            </header>
        </div>
    </div>
    <div v-else>
        <p>No user logs found.</p>
    </div>
</div>