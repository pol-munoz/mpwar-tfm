import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "input", "icon" ]

    toggle() {
        if (this.inputTarget.type === 'password') {
            this.inputTarget.type = 'text'
            this.iconTarget.classList.replace('fa-eye', 'fa-eye-slash')
        } else {
            this.inputTarget.type = 'password'
            this.iconTarget.classList.replace('fa-eye-slash', 'fa-eye')
        }
    }
}
