import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ["close"]

    close(event) {
        if (event.target.id === 'modal') {
            this.closeTarget.click()
        }
    }
}
