import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ["iframe"]
    static values = { mercure: String, topic: String, pub: String, sub: String }

    initialize() {
        this.iframeTarget.contentWindow[this.pubValue] = (message) => {
            fetch(window.parent.location.href, {
                method: 'POST',
                body: JSON.stringify(message)
            })
                .then(() => {})
                .catch(error => console.error(error.message))
        }

        const eventSource = new EventSource(this.mercureValue + '?topic=' + encodeURIComponent(this.topicValue));
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            this.iframeTarget.contentWindow[this.subValue](JSON.parse(event.data));
        }
    }
}
