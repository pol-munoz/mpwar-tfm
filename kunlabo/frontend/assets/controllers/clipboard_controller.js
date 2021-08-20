import { Controller } from 'stimulus';

export default class extends Controller {
    static values = { text: String }

    copy() {
        navigator.clipboard.writeText(this.textValue)
            .then(
                () => {},
                error => console.error(error.message)
            )
    }
}
