import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ["overlay", "placeholder", "files", "progress"]

    initialize() {
        this.uploading = 0
        this.uploaded = 0
        this.dragCount = 0
    }

    dragEnter() {
        if (this.dragCount === 0) {
            this.overlayTarget.style = "display: flex"
        }
        this.dragCount++
    }

    dragLeave() {
        this.dragCount--
        if (this.dragCount === 0) {
            this.overlayTarget.style = ""
        }
    }

    drag(event) {
        event.preventDefault()
    }

    drop(event) {
        this.dragCount = 0
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
                    this.renderFile(path, file.name)
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

    renderFile(path, name) {
        let file = document.getElementById(path + name)

        if (file !== null) {
            file.lastElementChild.innerHTML = 'now'
            return
        }

        let parts = path.substr(1, path.length - 2).split("/")
        let parent = this.filesTarget

        if (parts[0] !== "") {
            let i = 0
            let p = "/" + parts[i]
            let next = document.getElementById(p)
            let last = parent
            let white = true

            while (next != null) {
                i++
                last = next
                white = !white
                p += "/" + parts[i]
                next = document.getElementById(p)
            }

            parent = last

            for (let j = i; j < parts.length; j++) {
                let html = '<div class="Files-folder Files-folder-closed" style="background: ' + (white ? 'white' : '#F5F6F9') + '">\n' +
                '    <div class="Files-folder-header">\n' +
                '        <i class="fas fa-folder Files-folder-button" data-action="click->files#toggleFolder"></i>\n' +
                '        <p class="Files-text"><strong>' + parts[j] + '</strong></p>\n' +
                '    </div>\n' +
                '    <div class="Files-folder-contents" id="' + p + '">\n' +
                '    </div>\n' +
                '</div>'
                parent.appendChild(this.createElementFromHTML(html))
                parent = document.getElementById(p)
                p += "/" + parts[j]
                white = !white
            }
        }

        let html = '<div class="App-columned Files-file" id="' + path + name + '">\n' +
            '    <p class="Files-text Files-name">' + name + '</p>\n' +
            '    <p class="Files-text Files-date">Now</p>\n' +
            '    <p class="Files-text Files-date">Now</p>\n' +
            '</div>'
        parent.appendChild(this.createElementFromHTML(html))
    }
    createElementFromHTML(string) {
        let div = document.createElement('div')
        div.innerHTML = string.trim()

        return div.firstChild
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
