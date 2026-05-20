function isValidEmail(user_email) {
    const email_pattern = /^[a-zA-Z0-9]*@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/;
    return email_pattern.test(user_email);
}

function isValidContactNumber(user_contactNum) {
    const contactNum_pattern = /^(9)\d{9}$/;
    return contactNum_pattern.test(user_contactNum);
}

function isValidUsername(user_username) {
    const username_pattern = /^[A-Za-z][A-Za-z0-9_]{7,29}$/;
    return username_pattern.test(user_username);
}

function isValidPassword(user_password) {
    const password_pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*]).{8,}$/;
    return password_pattern.test(user_password);
}

const app = Vue.createApp({
    data() {
        return {
            isActive: false,
            visible: false,
            role: '',
            surname: '',
            firstname: '',
            position: '',
            email: '',
            contactNum: '',
            username: '',
            submit: false,

            // username checker
            available: null,
            error: '',

            // input validation
            validPosition: true,
            validEmail: true,
            validContactNum: true,
            validUsername: true
        }
    },
    methods: {
        isEmptyFirstname(firstname) {
            return firstname === "";
        },
        isEmptySurname(surname) {
            return surname === '';
        },
        isValidRole() {
            return this.role === '';
        },
        isValidPosition(position) {
            return position === '';
        },
        validateEmail(email) {
            return isValidEmail(email);
        },
        validateContactNum(contactNum) {
            return isValidContactNumber(contactNum);
        },
        validateUsername(username) {
            return isValidUsername(username);
        },
        async checkUsername() {
            if (this.username.length > 0) {
                const response = await axios.get('../admin/controllers/username_checker.php', {
                    params: {username: this.username}
                });
                this.available = response.data.available;
                this.error = '';
                console.log(response);
            } else {
                this.available = null;
            }
        },
        addUser() {
            $('#createUser').addClass('is-active');
            this.submit = true;
            this.isActive = true;

            if (!isValidEmail(this.email) || !isValidContactNumber(this.contactNum) || !isValidUsername(this.username)) {
                console.log("Invalid Email/Contact Number/Username format.");
                alert("Invalid Email/Contact Number/Username format.");
                return;
            } else if (this.isValidPosition(this.position)) {
                console.log("Undefined position.");
            }

            const userData = {
                role: this.role,
                surname: this.surname,
                firstname: this.firstname,
                position: this.position,
                email: this.email,
                contactNum: this.contactNum,
                username: this.username
            };

            axios.post("../admin/controllers/create_user.php", userData)
                .then(response => {
                    console.log(response.data);
                    alert("Hello, " + response.data.username + "!\nPlease take note of your password: " + response.data.password);

                    this.role = '';
                    this.surname = '';
                    this.firstname = '';
                    this.position = '';
                    this.email = '';
                    this.contactNum = '';
                    this.username = '';
                })

                .catch(error => {
                    console.error("Error adding user: ", error);
                })
        },
    },
    watch: {
        contactNum(value) {
            this.validContactNum = this.validateContactNum(value);
        },
        email(value) {
            this.validEmail = this.validateEmail(value);
        },
        username(value) {
            this.validUsername = this.validateUsername(value);
        },
    }
});
app.mount('#adminPage');

/* =============================================================== */
const userUpdate = Vue.createApp({
    data() {
        return {
            isRoleDisabled: true,
            searchQuery: '',
            users: [],
            position: '',
            selectedUser: null,
            autocompleteType: 'new-password',
            available: null,
            error: '',
            validNewContactNum: true,
            validNewEmail: true,
            validNewUsername: true,
            positionGroups: [
                {
                    label: 'Commissioned Officers',
                    options: [
                        {value: 'PGEN', label: 'Police General'},
                        {value: 'PLTGEN', label: 'Police Lieutenant General'},
                        {value: 'PMGEN', label: 'Police Major General'},
                        {value: 'PBGEN', label: 'Police Brigadier General'},
                        {value: 'PCOL', label: 'Police Colonel'},
                        {value: 'PLTCOL', label: 'Police Lieutenant Colonel'},
                        {value: 'PMAJ', label: 'Police Major'},
                        {value: 'PCPT', label: 'Police Captain'},
                        {value: 'PLT', label: 'Police Lieutenant'}
                    ]
                },
                {
                    label: 'Non-Commissioned Officers',
                    options: [
                        {value: 'PEMS', label: 'Police Executive Master Sergeant'},
                        {value: 'PCMS', label: 'Police Chief Master Sergeant'},
                        {value: 'PSMS', label: 'Police Senior Master Sergeant'},
                        {value: 'PMSg', label: 'Police Master Sergeant'},
                        {value: 'PSSg', label: 'Police Staff Sergeant'},
                        {value: 'PCpl', label: 'Police Corporal'},
                        {value: 'Patwmn', label: 'Patrolwoman'},
                        {value: 'Patmn', label: 'Patrolman'}
                    ]
                }
            ],

        }
    },
    methods: {
        searchUsers() {
            if (this.searchQuery.trim() === '') {
                this.users = [];
                return;
            }
            fetch('../admin/search_user.php?q=' + encodeURIComponent(this.searchQuery))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received user data: ', data);
                    this.users = data;
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                });
        },
        openEditModal(user) {
            this.selectedUser = user;
            this.selectedUser.newRole = user.ROLE;
            this.selectedUser.newSurname = user.SURNAME;
            this.selectedUser.newFirstname = user.FIRSTNAME;
            this.selectedUser.newPosition = user.POSITION;
            this.selectedUser.newEmail = user.EMAIL;
            this.selectedUser.newContactNum = user.CONTACTNUM;
            this.selectedUser.newUsername = user.USERNAME;
            $('#editUserModal').addClass('is-active');
        },
        isEmptyNewFirstname(firstname) {
            return firstname === "";
        },
        isEmptyNewSurname(surname) {
            return surname === '';
        },
        isValidNewPosition() {
            return this.selectedUser.newPosition !== '';
        },
        validateNewContactNum(contactNum) {
            return isValidContactNumber(contactNum);
        },
        validateNewEmail(email) {
            return isValidEmail(email);
        },
        validateNewUsername(username) {
            return isValidUsername(username);
        },
        validatePassword(password) {
            return isValidPassword(password);
        },
        passwordChecker(password, retype) {
            return password === retype;
        },
        cancelEdit() {
            this.selectedUser = null;
            $('#editUserModal').removeClass('is-active');
        },
        disableRoleField() {
            this.isRoleDisabled = this.selectedUser !== null;
        },
        async checkUsername() {
            if (this.selectedUser.newUsername.length > 0) {
                const response = await axios.get('../admin/controllers/username_checker.php', {
                    params: {username: this.selectedUser.newUsername}
                });
                this.available = response.data.available;
                this.error = '';
                console.log(response);
            } else {
                this.available = null;
            }
        },
        updateUser() {
            if (!isValidEmail(this.selectedUser.newEmail) || !isValidContactNumber(this.selectedUser.newContactNum) || !isValidUsername(this.selectedUser.newUsername)) {
                console.log("Invalid Email/Contact Number/Username format.");
                alert("Invalid Email/Contact Number/Username format.");
                return;
            }

            let requestData = {
                USER_ID: this.selectedUser.USER_ID,
                ROLE: this.selectedUser.newRole,
                SURNAME: this.selectedUser.newSurname,
                FIRSTNAME: this.selectedUser.newFirstname,
                POSITION: this.selectedUser.newPosition,
                EMAIL: this.selectedUser.newEmail,
                CONTACTNUM: this.selectedUser.newContactNum,
                USERNAME: this.selectedUser.newUsername
            };
            if (!this.selectedUser.newPassword || this.selectedUser.newPassword.trim() === '') {
                requestData.PASSWORD = this.selectedUser.newPassword;
            } else if (this.selectedUser.newPassword !== '') {
                if (!this.passwordChecker()) {
                    console.log("Passwords do not match");
                    alert("Passwords do not match.");
                    return;
                } else if (!this.validatePassword(this.selectedUser.newPassword)) {
                    console.log("Password is too weak");
                    alert("Password is too weak.");
                    return;
                }
                requestData.PASSWORD = this.selectedUser.newPassword;
            }

            axios.put('../admin/controllers/update_user.php', requestData)
                .then(response => {
                    console.log('Response:', response.data);
                    this.selectedUser.ROLE = this.selectedUser.newRole;
                    this.selectedUser.SURNAME = this.selectedUser.newSurname;
                    this.selectedUser.FIRSTNAME = this.selectedUser.newFirstname;
                    this.selectedUser.POSITION = this.selectedUser.newPosition;
                    this.selectedUser.EMAIL = this.selectedUser.newEmail;
                    this.selectedUser.USERNAME = this.selectedUser.newUsername;
                    if (requestData.PASSWORD) {
                        this.selectedUser.PASSWORD = requestData.PASSWORD;
                    }
                    alert("User updated successfully.");
                    this.cancelEdit();
                })

                .catch(error => {
                    console.log('Error updating user:', error);
                });
        },
        deleteUser(user) {
            if (confirm('Are you sure you want to delete this user?')) {
                axios.delete(`../admin/controllers/delete_user.php?id=${user.USER_ID}`)
                    .then(response => {
                        console.log('User deleted successfully:', response.data);
                        this.selectedUser = null;
                    })
                    .catch(error => {
                        console.error('Error deleting user:', error);
                    });
            }
        },
        toggle(password) {
            const input = document.getElementById(password);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        },
        mounted() {
            this.searchUsers();
            this.disableRoleField();
            this.autocompleteType = 'new-password';
        }
    },

});
userUpdate.mount('#userUpdate');

