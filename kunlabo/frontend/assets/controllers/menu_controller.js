import { Controller } from 'stimulus';

export default class extends Controller {

    initialize() {
        this.menuOpen = false
        this.last = null
    }

    toggleMenu(event) {
        let last = this.last

        if (this.menuOpen) {
            this.closeMenu()
        }

        let parent = event.target.parentNode

        if (parent.id !== last) {
            parent.lastElementChild.style = "display: flex; right: 70px; top: 0"
            this.last = parent.id
            this.menuOpen = true
        }
    }

    closeMenu() {
        document.getElementById(this.last).lastElementChild.style = ""

        this.last = null
        this.menuOpen = false
    }

    closeMenuIfOutside(event) {
        // A bit unsustainable sorry not sorry
        if (!event.target.classList.contains('App-menu') &&
            !event.target.classList.contains('App-menu-option') &&
            !event.target.classList.contains('App-menu-option-icon') &&
            !event.target.classList.contains('App-menu-option-text') &&
            !event.target.classList.contains('App-kebab-button')) {
            this.closeMenu()
        }
    }
}
