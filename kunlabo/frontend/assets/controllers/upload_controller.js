import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ["overlay"]

    // TODO upload files
    // - show progress and render result per file
    // - ALSO placeholder if the engine is empty: show an upload icon and some text like "Drop some files or folders to upload them"

    dragEnter() {
        this.overlayTarget.style = "display: flex"
    }

    dragLeave() {
        this.overlayTarget.style = ""
    }

    drag(event) {
        event.preventDefault()
    }

    drop(event) {
        this.overlayTarget.style = ""
        event.preventDefault()

        this.processItems(event.dataTransfer.items)
    }

    processItems(items) {
        for (let i = 0; i < items.length; i++) {
            let item = items[i].webkitGetAsEntry()

            if (item) {
                this.processItem(item)
            }
        }
    }

    processItem(item, path = "/") {
        if (item.isFile) {
            item.file(file => {
                let formData = new FormData()

                formData.append('file', file)
                formData.append('path', path)

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                })
                    .then(() => {})
                    .catch(() => {})
            })
        }
        if (item.isDirectory) {
            let directoryReader = item.createReader();
            directoryReader.readEntries((entries) => {
                entries.forEach((entry) => {
                    this.processItem(entry, path + item.name + "/")
                })
            })
        }
    }
}
