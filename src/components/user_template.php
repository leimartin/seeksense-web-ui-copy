<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<section class="modal-card-body px-5">
    <div class="control mb-5">
        <span class="has-text-weight-bold mr-3">Role:</span>
        <label class="radio mr-3">
            <input type="radio" name="role" value="Admin" v-model="role"/>
            Admin
        </label>
        <label class="radio mr-3">
            <input type="radio" name="role" value="Standard" v-model="role" :disabled="standardDisabled"/>
            Standard
        </label>
        <span v-if="isValidRole()" class="help is-danger">Please select a role.</span>
    </div>


    <div class="columns">
        <div class="column is-5">
            <div class="control has-icons-left">
                <input type="text" v-model="surname" class="input" placeholder="Doe" required>
                <p v-if="isEmptySurname(surname)"
                   class="help is-danger">Surname cannot be blank.</p>
                <label class="label">surname</label>
                <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
            </div>
        </div>
        <div class="column">
            <div class="control has-icons-left">
                <input type="text" v-model="firstname" class="input" placeholder="Jane" required>
                <p v-if="isEmptyFirstname(firstname)" class="help is-danger">First name cannot be blank.</p>
                <label class="label">first name</label>
                <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
            </div>
        </div>
    </div>

    <div class="select is-fullwidth">
        <select v-model="position" id="position" required>
            <optgroup label="Commissioned Officers">
                <option value="PGEN">Police General</option>
                <option value="PLTGEN">Police Lieutenant General</option>
                <option value="PMGEN">Police Major General</option>
                <option value="PBGEN">Police Brigadier General</option>
                <option value="PCOL">Police Colonel</option>
                <option value="PLTCOL">Police Lieutenant Colonel</option>
                <option value="PMAJ">Police Major</option>
                <option value="PCPT">Police Captain</option>
                <option value="PLT">Police Lieutenant</option>
            </optgroup>
            <optgroup label="Non-Commissioned Officers">
                <option value="PEMS">Police Executive Master Sergeant</option>
                <option value="PCMS">Police Chief Master Sergeant</option>
                <option value="PSMS">Police Senior Master Sergeant</option>
                <option value="PMSg">Police Master Sergeant</option>
                <option value="PSSg">Police Staff Sergeant</option>
                <option value="PCpl">Police Corporal</option>
                <option value="Patwmn">Patrolwoman</option>
                <option value="Patmn">Patrolman</option>
            </optgroup>
        </select>
    </div>
    <span v-if="isValidPosition()" class="help is-danger">Please select position in the list.</span>
    <label class="label mb-5">position</label>

    <div class="control has-icons-left">
        <input type="email" v-model="email" class="input" placeholder="someone@gmail.com"
               pattern="^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$" required>
        <p v-if="email && !validEmail" class="help is-danger">Please enter a valid email.</p>
        <label class="label mb-5">email
            <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
    </div>

    <div class="row ">
        <div class="columns">
            <div class="column is-one-fifth">
                <div class="control has-icons-left">
                    <input type="text" class="input" value="+63" readonly>
                    <span class="icon is-small is-left"><i class="fas fa-phone"></i></span></div>
            </div>

            <div class="column">
                <input type="tel" v-model="contactNum" class="input" pattern="^(9)\d{9}$"
                       placeholder="9946702011"
                       required>
            </div>
        </div>
    </div>
    <p v-if="contactNum && !validContactNum" class="help is-danger">Please enter a valid contact number.</p>
    <label class="label mb-5">contact number</label>

    <div class="control has-icons-left">
        <input type="text" v-model="username" @input="checkUsername" class="input" placeholder="janedoe123" required>
        <p v-if="username && !validUsername" class="help is-danger">username must be unique.</p>
        <section v-else>
            <p v-if="available === true" class="help is-success">Username is available</p>
            <p v-if="available === false" class="help is-danger">Username is unavailable</p>
            <p v-if="error">{{ error }}</p>
        </section>
        <label class="label mb-1">username</label>
        <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
    </div>
</section>

<footer class="modal-card-foot field is-grouped is-grouped-right">
    <button type="submit" class="button is-info" @click="addUser">Create User</button>
</footer>
