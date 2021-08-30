import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ["iframe"]
    static values = { mercure: String, topic: String, pub: String, sub: String, persist: String }

    initialize() {
        this.reloading = false

        const KunlaboAction = {
            MESSAGE: 'MESSAGE',
            LOG: 'LOG',
            PERSIST: 'PERSIST',
        }
        Object.freeze(KunlaboAction)

        this.iframeTarget.contentWindow[this.pubValue] = (body, actions = [KunlaboAction.MESSAGE]) => {
            for (let action of actions) {
                if (!KunlaboAction[action]) {
                    throw new RangeError('Invalid action: ' + action)
                }
            }

            fetch(window.parent.location.href, {
                method: 'POST',
                body: JSON.stringify({ actions, body })
            })
            .then(response => {
                if (response.status > 400) {
                    if (!this.reloading) {
                        this.reloading = true
                        location.reload()
                    }
                }
            })
            .catch(error => console.error(error.message))
        }

        this.iframeTarget.contentWindow.addEventListener('load', () => {
            window.setTimeout(() => {
                fetch(this.persistValue)
                    .then(response => {
                        if (response.status === 200) {
                            response.json().then(data => this.iframeTarget.contentWindow.onPersistLoaded(data))
                        }
                    })
                    .catch(error => console.error(error.message))
            }, 0)
        })

        this.iframeTarget.contentWindow.KunlaboAction = KunlaboAction

        const eventSource = new EventSource(this.mercureValue + '?topic=' + encodeURIComponent(this.topicValue));
        eventSource.onmessage = event => {
            // Will be called every time an update is published by the server
            this.iframeTarget.contentWindow[this.subValue](JSON.parse(event.data));
        }
    }
}
