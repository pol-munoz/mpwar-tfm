import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ["button", "form", "input"]

    show() {
        this.formTarget.style = "display: flex"
        this.buttonTarget.style = "display: none"
        this.inputTarget.focus()
    }

    hide() {
        this.formTarget.style = ""
        this.buttonTarget.style = ""
    }

    keyUp(event) {
        if (event.key === 'Escape') {
            this.hide()
            this.inputTarget.value = ""
        }
    }

    blur() {
        this.hide()
        this.inputTarget.value = ""
    }

    submit(event) {
        if (this.inputTarget.value.length === 0) {
            this.hide()
            event.preventDefault()
        } else if (this.inputTarget.value.length > 255) {
            alert('Name must not exceed 255 characters')
            event.preventDefault()
        }
    }
}
