import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ["overlay", "placeholder", "files", "progress", "menu"]
    static values = { main: String }

    initialize() {
        this.uploading = 0
        this.uploaded = 0
        this.dragCount = 0
        this.menuOpen = false
        this.lastFile = null
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
                .catch(error => {
                    this.uploaded++
                    this.updateUploading()
                    console.error(error.message)
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
            file.lastElementChild.previousElementSibling.innerHTML = 'now'
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
                '        <i class="fas fa-fw fa-folder Files-folder-button" data-action="click->files#toggleFolder"></i>\n' +
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

        let full = path + name
        let html

        if (full === this.mainValue) {
            html = '<div class="App-four-columns-right Files-file" id="' + full + '">\n' +
                '    <p class="Files-text Files-name" id="main">\n' +
                '        <i class="fas fa-fw fa-star Files-main-icon"></i>\n' +
                '        ' + name + '\n' +
                '    </p>\n' +
                '    <p class="Files-text Files-date">now</p>\n' +
                '    <p class="Files-text Files-date">now</p>\n' +
                '    <i class="fas fa-ellipsis-v fa-fw App-kebab-button" data-action="click->files#toggleFileMenu"></i>\n' +
                '</div>'
        } else {
            html = '<div class="App-four-columns-right Files-file" id="' + full + '">\n' +
                '    <p class="Files-text Files-name">' + name + '</p>\n' +
                '    <p class="Files-text Files-date">now</p>\n' +
                '    <p class="Files-text Files-date">now</p>\n' +
                '    <i class="fas fa-ellipsis-v fa-fw App-kebab-button" data-action="click->files#toggleFileMenu"></i>\n' +
                '</div>'
        }
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
            }, 500)
        } else {
            this.progressTarget.parentNode.style = ""
            this.progressTarget.style = "width: " + ((this.uploaded / this.uploading) * 100) + "%"
        }
    }

    toggleFolder(event) {
        let isOpen = event.target.classList.contains('fa-folder-open')
        if (isOpen) {
            this.closeFolderFromIcon(event.target)
        } else {
            event.target.classList.replace('fa-folder', 'fa-folder-open')
            event.target.parentNode.parentNode.classList.remove('Files-folder-closed')
        }
    }

    closeFolderFromIcon(icon) {
        icon.classList.replace('fa-folder-open', 'fa-folder')
        icon.parentNode.parentNode.classList.add('Files-folder-closed')

        let contents = icon.parentNode.parentNode.lastElementChild

        for (let i = 0; i < contents.childElementCount; i++) {
            let child = contents.children.item(i)
            if (child.classList.contains('Files-folder') && !child.classList.contains('Files-folder-closed')) {
                this.closeFolderFromIcon(child.firstElementChild.firstElementChild)
            }
        }
    }

    toggleFileMenu(event) {
        if (this.menuOpen && event.target.parentNode.id === this.lastFile) {
            this.closeMenu()
        } else {
            let bottom = event.target.getBoundingClientRect().bottom
            let scroll = this.filesTarget.parentNode.parentNode.scrollTop
            this.menuTarget.style = "display: flex; right: 70px; top: " + (bottom - 40 + scroll) + "px"
            this.lastFile = event.target.parentNode.id

            this.menuOpen = true
        }
    }

    closeMenu() {
        this.menuTarget.style = ""
        this.lastFile = null
        this.menuOpen = false
    }

    setAsMain() {
        let html = '<i class="fas fa-fw fa-star Files-main-icon"></i>'
        let lastName = document.getElementById(this.lastFile).firstElementChild
        let main = document.getElementById('main')

        if (lastName.firstElementChild === null) {
            fetch(this.injectBeforeId('file/main/'), {
                method: 'POST',
                body: this.lastFile,
                credentials: 'include'
            })
            .then(() => {
                if (main) {
                    main.id = ''
                    main.removeChild(main.firstElementChild)
                }
                lastName.innerHTML = html + lastName.innerHTML
                lastName.id = 'main'
            })
            .catch(error => console.error(error.message))
        }
        this.closeMenu()
    }

    delete() {
        let file = this.lastFile
        if (confirm('Are you sure you want to delete this file?\n\n"' + this.lastFile + '"')) {
            fetch(this.injectBeforeId('file/delete/'), {
                method: 'POST',
                body: file,
                credentials: 'include'
            })
            .then(() => {
                let element = document.getElementById(file)
                let parent = element.parentElement

                parent.removeChild(element)

                while (!parent.classList.contains('Files') && parent.childElementCount === 0) {
                    element = parent.parentElement
                    parent = parent.parentElement.parentElement
                    parent.removeChild(element)
                }

                if (parent.classList.contains('Files') && parent.childElementCount === 1) {
                    parent.style.display = "none"
                    this.placeholderTarget.style = "display: flex"
                }
            })
            .catch(error => console.error(error.message))
        }
        this.closeMenu()
    }

    closeMenuIfOutside(event) {
        // A bit unsustainable sorry not sorry
        if (this.menuOpen &&
            !event.target.classList.contains('App-menu') &&
            !event.target.classList.contains('App-menu-option') &&
            !event.target.classList.contains('App-menu-option-icon') &&
            !event.target.classList.contains('App-menu-option-text') &&
            !event.target.classList.contains('App-kebab-button')) {
            this.closeMenu()
        }
    }

    injectBeforeId(prefix) {
        let path = window.location.href
        let parts = path.split('/')
        let id = parts[5]
        let index = path.indexOf(id)

        return path.slice(0, index) + prefix + id
    }
}
