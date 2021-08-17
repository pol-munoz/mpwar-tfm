import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ["overlay", "placeholder", "files", "progress"]

    initialize() {
        this.uploading = 0
        this.uploaded = 0
    }

    dragEnter(event) {
        this.overlayTarget.style = "display: flex"
        event.preventDefault()
    }

    dragLeave(event) {
        this.overlayTarget.style = ""
        event.preventDefault()
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
                this.uploading++
                this.updateUploading()

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                })
                .then(() => {
                    this.placeholderTarget.style = "display: none"
                    this.filesTarget.style = ""
                    this.uploaded++
                    this.updateUploading()
                    this.renderFile(path)
                })
                .catch(() => {
                    this.uploaded++
                    this.updateUploading()
                })
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

    // TODO drag and drop bug: overlay is closed when hovering over stuff
    // TODO render uploaded file (if it doesn't exist yet!!!! use path as id???)
    renderFile(path) {

    }

    updateUploading() {
        if (this.uploaded === this.uploading) {
            this.progressTarget.style = "width: 100%"

            window.setTimeout(() => {
                if (this.uploaded === this.uploading) {
                    this.progressTarget.parentNode.style = "display: none"
                    this.uploaded = 0
                    this.uploading = 0
                }
            }, 1000)
        } else {
            this.progressTarget.parentNode.style = ""
            this.progressTarget.style = "width: " + ((this.uploaded / this.uploading) * 100) + "%"
        }
    }

    toggleFolder(event) {
        let isOpen = event.target.classList.contains('fa-folder-open')
        if (isOpen) {
            event.target.classList.replace('fa-folder-open', 'fa-folder')
            event.target.parentNode.parentNode.classList.add('Files-folder-closed')
        } else {
            event.target.classList.replace('fa-folder', 'fa-folder-open')
            event.target.parentNode.parentNode.classList.remove('Files-folder-closed')
        }
    }
}
