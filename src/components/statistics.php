<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<div class="columns has-text-centered" id="statistics">
    <div class="column">
        <p class="subtitle is-6 has-text-grey">SEARCHES</p>
        <p class="title is-4"> {{ stats.searches }} </p>
    </div>

    <div class="column">
        <p class="subtitle is-6 has-text-grey">POSTS</p>
        <p class="title is-4">{{ stats.posts }}</p>
    </div>

    <div class="column">
        <p class="subtitle is-6 has-text-grey">FACES</p>
        <p class="title is-4">{{ stats.faces }}</p>
    </div>
</div>