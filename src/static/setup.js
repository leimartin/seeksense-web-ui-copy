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

const app = Vue.createApp({
    data() {
        return {
            standardDisabled: true,
            visible: false,
            role: '',
            surname: '',
            firstname: '',
            position: '',
            email: '',
            contactNum: '',
            username: '',

            // username checker
            available: null,
            error: '',

            // input validation
            isActive: false,
            submit: false,
            validPosition: true,
            validEmail: true,
            validContactNum: true,
            validUsername: true
        }
    },
    methods: {
        isValidRole() {
            return this.role === '';
        },
        isEmptyFirstname(firstname) {
            return firstname === "";
        },
        isEmptySurname(surname) {
            return surname === '';
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
            this.submit = true;
            this.isActive = true;

            const userData = {
                role: this.role,
                surname: this.surname,
                firstname: this.firstname,
                position: this.position,
                email: this.email,
                contactNum: this.contactNum,
                username: this.username
            };

            axios.post('setup_admin.php', userData)
                .then(response => {
                    console.log(response.data);
                    alert("Hello, " + response.data.username + "!\nPlease take note of your password: " + response.data.password);

                    this.role = 'Admin';
                    this.surname = '';
                    this.firstname = '';
                    this.position = '';
                    this.email = '';
                    this.contactNum = '';
                    this.username = '';
                    window.location.href = 'index.php';
                })

                .catch(error => {
                    console.error("Error adding user: ", error);
                })
        }
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
        }
    }
});
app.mount('#setupPage');
