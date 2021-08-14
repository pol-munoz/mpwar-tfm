import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "name", "nameError", "email", "emailError", "password", "passwordError" ]

    initialize() {
        this.nameError = true
        this.emailError = true
        this.passwordError = true

        this.passwordUpper = false
        this.passwordNumber = false
        this.passwordLower = false
        this.passwordSpecial = false
    }

    nameChanged() {
        if (this.nameTarget.value.length > 255) {
            this.nameError = true
            this.nameErrorTarget.parentNode.style = "opacity: 1"
            this.nameErrorTarget.innerText = "Must not exceed 255 characters"
        } else {
            this.nameError = false
            this.nameErrorTarget.parentNode.style = ""
        }
    }

    nameBlur() {
        if (this.nameTarget.value.length === 0) {
            this.nameError = true
            this.nameErrorTarget.parentNode.style = "opacity: 1"
            this.nameErrorTarget.innerText = "Required"
        }
    }

    emailChanged() {
        if (this.emailTarget.value.length > 255) {
            this.emailError = true
            this.emailErrorTarget.parentNode.style = "opacity: 1"
            this.emailErrorTarget.innerText = "Must not exceed 255 characters"
        } else if (this.emailTarget.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            this.emailError = false
            this.emailErrorTarget.parentNode.style = ""
        }
    }

    emailBlur() {
        if (this.emailTarget.value.length === 0) {
            this.emailError = true
            this.emailErrorTarget.parentNode.style = "opacity: 1"
            this.emailErrorTarget.innerText = "Required"
        } else if (!this.emailTarget.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            this.emailError = true
            this.emailErrorTarget.parentNode.style = "opacity: 1"
            this.emailErrorTarget.innerText = "Invalid email"
        }
    }

    passwordChanged() {
        if (this.passwordTarget.value.length > 72) {
            this.passwordError = true
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = "Must not exceed 72 characters"
        } else {
            this.passwordError = false
            this.passwordErrorTarget.parentNode.style = ""
        }
        if (this.passwordTarget.value.length > 0) {
            if (this.passwordTarget.value.length < 8) {
                this.passwordError = true
                this.passwordErrorTarget.parentNode.style = "opacity: 1"
                this.passwordErrorTarget.innerText = "Must be at least 8 characters long"
            } else {
                this.showPasswordError()
            }
        } else {
            this.passwordError = false
            this.passwordErrorTarget.parentNode.style = ""
        }
    }

    passwordBlur() {
        if (this.passwordTarget.value.length === 0) {
            this.passwordError = true
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = "Required"
        } else if (this.passwordTarget.value.length < 8) {
            this.passwordError = true
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = "Must be at least 8 characters long"
        } else {
            this.showPasswordError()
        }
    }

    showPasswordError() {
        this.passwordLower = this.passwordTarget.value.match(/[a-z]/g)
        this.passwordUpper = this.passwordTarget.value.match(/[A-Z]/g)
        this.passwordNumber = this.passwordTarget.value.match(/[0-9]/g)
        this.passwordSpecial = this.passwordTarget.value.match(/[^\w]/g)

        let error = ""

        if (!this.passwordLower) {
            error += "lowercase letters, "
        }
        if (!this.passwordUpper) {
            error += "uppercase letters, "
        }
        if (!this.passwordNumber) {
            error += "digits, "
        }
        if (!this.passwordSpecial) {
            error += "special characters, "
        }

        error = "Must contain: " + error.charAt(0).toUpperCase() + error.slice(1, -2)

        if (!this.passwordLower || !this.passwordUpper || !this.passwordNumber || !this.passwordSpecial) {
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = error
        } else {
            this.passwordErrorTarget.parentNode.style = "opacity: 0"
        }
    }

    submit(event) {
        let passwordOk = this.passwordLower && this.passwordUpper && this.passwordNumber && this.passwordSpecial

        if (this.nameError || this.emailError || this.passwordError || !passwordOk) {
            event.preventDefault()
            this.nameBlur()
            this.emailBlur()
            this.passwordBlur()
        }
    }
}
