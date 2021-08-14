import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "name", "nameError", "email", "emailError", "password", "passwordError" ]

    initialize() {
        this.nameError = false
        this.emailError = false
        this.passwordError = false
        this.passwordUpper = true
        this.passwordNumber = true
        this.passwordLower = true
        this.passwordSpecial = true
    }

    nameChanged() {
        if (!this.nameError && this.nameTarget.value.length > 255) {
            this.nameError = true
            this.nameErrorTarget.parentNode.style = "opacity: 1"
            this.nameErrorTarget.innerText = "Must not exceed 255 characters"
        }
        if (this.nameError && this.nameTarget.value.length < 255) {
            this.nameError = false
            this.nameErrorTarget.parentNode.style = ""
        }
    }

    nameBlur() {
        if (!this.nameError && this.nameTarget.value.length === 0) {
            this.nameError = true
            this.nameErrorTarget.parentNode.style = "opacity: 1"
            this.nameErrorTarget.innerText = "Required"
        }
    }

    emailChanged() {
        if (this.emailError && this.emailTarget.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            this.emailError = false
            this.emailErrorTarget.parentNode.style = ""
        }
    }

    emailBlur() {
        if (!this.emailError && this.emailTarget.value.length === 0) {
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
        if (!this.passwordError && this.passwordTarget.value.length > 72) {
            this.passwordError = true
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = "Must not exceed 72 characters"
        }
        if (this.passwordError && this.passwordTarget.value.length < 72) {
            this.passwordError = false
            this.passwordErrorTarget.parentNode.style = ""
        }

        if (!this.passwordLower && this.passwordTarget.value.match(/[a-z]/g)) {
            this.passwordLower = true
            this.showPasswordError()
        }
        if (!this.passwordUpper && this.passwordTarget.value.match(/[A-Z]/g)) {
            this.passwordUpper = true
            this.showPasswordError()
        }
        if (!this.passwordNumber && this.passwordTarget.value.match(/[0-9]/g)) {
            this.passwordNumber = true
            this.showPasswordError()
        }
        if (!this.passwordSpecial && this.passwordTarget.value.match(/[^\w]/g)) {
            this.passwordSpecial = true
            this.showPasswordError()
        }

    }

    passwordBlur() {
        if (!this.passwordError && this.passwordTarget.value.length === 0) {
            this.passwordError = true
            this.passwordErrorTarget.parentNode.style = "opacity: 1"
            this.passwordErrorTarget.innerText = "Required"
        } else {
            this.passwordLower = this.passwordTarget.value.match(/[a-z]/g)
            this.passwordUpper = this.passwordTarget.value.match(/[A-Z]/g)
            this.passwordNumber = this.passwordTarget.value.match(/[0-9]/g)
            this.passwordSpecial = this.passwordTarget.value.match(/[^\w]/g)

            this.showPasswordError()
        }
    }

    showPasswordError() {
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
}
