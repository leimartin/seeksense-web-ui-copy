<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]))
    die();
?>

<div id="userUpdate">
    <div class="columns">
        <div class="column auto">
            <p class="control has-icons-left">
                <input class="input" type="text" v-model="searchQuery" @input="searchUsers" placeholder="Search"/>
                <span class="icon is-small is-left"><i class="fas fa-search"></i></span>
            </p>
            <p class="is-italic is-size-7">e.g. Surname, Forename</p>
        </div>
        <div class="column auto"></div>
    </div>

    <ul v-if="users.length > 0">
        <li v-for="user in users" :key="user.ID">
            <div>
                <article class="media">
                    <div class="column is-1">
                        <div class="media-left">
                            <div class="column is-narrow is-vcentered">
                                <span class="icon has-text-grey-light" style="font-size: 2rem"><i
                                            class="fas fa-2x fa-user"></i></span>
                            </div>

                        </div>
                    </div>
                    <div class="column is-3">
                        <strong> {{ user.POSITION }} {{ user.FIRSTNAME }} <span style="text-transform: uppercase;">{{ user.SURNAME }}</span>
                        </strong>
                        <p class="is-italic is-size-7"> {{ user.ROLE }} </p>
                    </div>
                    <div class="column is-7">

                        <table class="table is-hoverable is-fullwidth">
                            <tr>
                                <td class=""> {{ user.EMAIL }}</td>
                                <td class=""> {{ user.CONTACTNUM }}</td>
                                <td class=""> {{ user.USERNAME }}</td>
                            </tr>
                            <tr>
                                <th class="is-italic is-size-7">EMAIL</th>
                                <th class="is-italic is-size-7">PHONE NO.</th>
                                <th class="is-italic is-size-7">USERNAME</th>
                            </tr>
                        </table>
                    </div>

                    <div class="column is-1">
                        <a @click="openEditModal(user)">Edit</a>
                    </div>
                </article>
            </div>
        </li>
    </ul>

    <div class="modal" id='editUserModal' v-if="selectedUser">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Update User Profile</p>
                <button class="delete" aria-label="close" @click="cancelEdit"></button>
            </header>
            <section class="modal-card-body px-5">
                <div class="control mb-5">
                    <span class="has-text-weight-bold mr-3">Role:</span>
                    <label class="radio mr-3">
                        <input type="radio" name="role" value="Admin" v-model="selectedUser.newRole"
                               :disabled="isRoleDisabled"/>
                        Admin
                    </label>
                    <label class="radio mr-3">
                        <input type="radio" name="role" value="Standard" v-model="selectedUser.newRole"
                               :disabled="isRoleDisabled"/>
                        Standard
                    </label>
                </div>

                <div class="columns">
                    <div class="column is-5">
                        <div class="control has-icons-left">
                            <input type="text" v-model="selectedUser.newSurname" class="input"
                                   placeholder="Doe" required>
                            <p v-if="isEmptyNewSurname(selectedUser.newSurname)"
                               class="help is-danger">Surname cannot be blank.</p>
                            <label class="label">surname</label>
                            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control has-icons-left">
                            <input type="text" v-model="selectedUser.newFirstname" class="input"
                                   placeholder="Jane"
                                   required>
                            <p v-if="isEmptyNewFirstname(selectedUser.newFirstname)"
                               class="help is-danger">First name cannot be blank.</p>
                            <label class="label">first name</label>
                            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                        </div>
                    </div>
                </div>

                <div class="select is-fullwidth">
                    <select v-model="selectedUser.newPosition" id="position" required>
                        <optgroup v-for="(group, index) in positionGroups" :label="group.label"
                                  :key="index">
                            <option v-for="(option, i) in group.options" :value="option.value" :key="i">{{
                                option.label }}
                            </option>
                        </optgroup>
                    </select>
                </div>
                <label for="user-position"></label>

                <p class="has-text-danger is-size-7"
                   v-if="selectedUser.newPosition && !isValidNewPosition(selectedUser.newPosition)">Please select
                    position in the
                    list.</p>
                <label class="label mb-5">position</label>

                <div class="control has-icons-left">
                    <input type="email" v-model="selectedUser.newEmail" class="input"
                           placeholder="someone@gmail.com" required>
                    <p v-if="selectedUser.newEmail && !validateNewEmail(selectedUser.newEmail)" class="help is-danger">
                        Please enter a valid
                        email.</p>
                    <label class="label mb-5">email</label>
                    <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                </div>

                <div class="row ">
                    <div class="columns">
                        <div class="column is-one-fifth">
                            <div class="control has-icons-left">
                                <input type="text" class="input" value="+63" readonly>
                                <span class="icon is-small is-left"><i class="fas fa-phone"></i></span>
                            </div>
                        </div>

                        <div class="column">
                            <input type="tel" v-model="selectedUser.newContactNum" class="input"
                                   placeholder="9946702011" required>
                        </div>
                    </div>
                </div>
                <p v-if="selectedUser.newContactNum && !validateNewContactNum(selectedUser.newContactNum)"
                   class="help is-danger">Please enter a valid
                    contact number.</p>
                <label class="label mb-5">contact number</label>

                <div class="control has-icons-left">
                    <input type="text" v-model="selectedUser.newUsername" @input="checkUsername" class="input"
                           placeholder="janedoe123" required>

                    <p v-if="selectedUser.newUsername && !validateNewUsername(selectedUser.newUsername)"
                       class="help is-danger">username must be
                        unique.</p>
                    <section v-else>
                        <p v-if="available === true" class="help is-success">username is available</p>
                        <p v-if="available === false" class="help is-danger">username is unavailable</p>
                        <p v-if="error">{{ error }}</p>
                    </section>

                    <label class="label mb-5">username</label>
                    <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                </div>

                <div class="control has-icons-left has-icons-right">
                    <input type="password" id="new-password" v-model="selectedUser.newPassword" class="input"
                           placeholder="Password" :autocomplete="autocompleteType">
                    <span class="icon is-small is-left"><i class="fa fa-lock"></i></span>
                    <span class="icon is-small is-right" style="pointer-events: all; cursor: pointer">
                        <i class="fa fa-fw fa-eye-slash field_icon toggle-password" @click="toggle('new-password')"></i>
                    </span>
                </div>
                <div v-if="selectedUser.newPassword && !validatePassword(selectedUser.newPassword)"
                     class="help is-danger">Weak password
                    <div class="box" v-else>
                        <p>Your password must contain:</p>
                        <ul class="has-text-success">✔ At least 8 characters</ul>
                        <ul class="has-text-success">✔ At least 3 of the following:
                            <li>&nbsp;&nbsp; ✔ Lowercase letters (a-z)</li>
                            <li>&nbsp;&nbsp; ✔ Uppercase letters (A-Z)</li>
                            <li>&nbsp;&nbsp; ✔ Numbers (0-9)</li>
                            <li>&nbsp;&nbsp; ✔ Special characters (e.g. !@#$%^&*)</li>
                        </ul>
                    </div>
                </div>
                <label class="label mb-5">password</label>

                <div class="control has-icons-left has-icons-right">
                    <input type="password" id="retype-password" v-model="selectedUser.retypePassword" class="input"
                           placeholder="Re-type Password" :autocomplete="autocompleteType">
                    <span class="icon is-small is-left"><i class="fa fa-lock"></i></span>
                    <span class="icon is-small is-right" style="pointer-events: all; cursor: pointer">
                        <i class="fa fa-fw fa-eye-slash field_icon toggle-password"
                           @click="toggle('retype-password')"></i>
                    </span>
                </div>
                <p class="has-text-danger is-size-7"
                   v-if="selectedUser.newPassword && selectedUser.retypePassword && !this.passwordChecker(selectedUser.newPassword, selectedUser.retypePassword)">
                    Passwords do not match</p>
                <label class="label mb-2">retype password</label>

            </section>

            <footer class="modal-card-foot field is-paddingless">
                <div class="column p-3">
                    <button class="button is-danger" @click="deleteUser(selectedUser)">Delete</button>
                </div>
                <div class="column is-flex is-justify-content-flex-end p-3">
                    <button class="button is-success" @click="updateUser(selectedUser)">Save</button>
                    <button class="button is-info" @click="cancelEdit">Cancel</button>
                </div>
            </footer>
        </div>
    </div>
</div>

