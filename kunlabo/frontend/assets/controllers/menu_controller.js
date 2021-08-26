import { Controller } from 'stimulus';

export default class extends Controller {

    static values = { deleteMessage: String }

    initialize() {
        this.menuOpen = false
        this.last = null
    }

    toggleMenu(event) {
        let last = this.last

        if (this.menuOpen) {
            this.closeMenu()
        }

        let parent = event.target.parentNode.parentNode

        if (parent.id !== last) {
            parent.lastElementChild.style = "display: flex; right: 30px; top: 40px"
            this.last = parent.id
            this.menuOpen = true
        }

        event.preventDefault()
    }

    closeMenu() {
        document.getElementById(this.last).lastElementChild.style = ""

        this.last = null
        this.menuOpen = false
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

    confirm(event) {
        let target = event.target

        if (!target.dataset.name) {
            target = target.parentNode
        }

        if (!confirm(this.deleteMessageValue + '\n\n"' + target.dataset.name + '"')) {
            event.preventDefault()
        }
    }
}
