import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "nav", "icon" ]

    initialize() {
        this.isOpen = (localStorage.getItem('openSidebar') ?? 'true') === 'true'
        this.renderSidebar()
        window.setTimeout(() => {
            this.navTarget.classList.add('App-nav-animated')
        }, 0)
    }

    toggle() {
        this.isOpen = !this.isOpen
        this.renderSidebar()
        localStorage.setItem('openSidebar', this.isOpen)
    }

    renderSidebar() {
        if (this.isOpen) {
            this.navTarget.style = ""
            this.iconTarget.classList.replace('fa-angle-double-right', 'fa-angle-double-left')
        } else {
            this.navTarget.style = "width: 35px"
            this.iconTarget.classList.replace('fa-angle-double-left', 'fa-angle-double-right')
        }
    }
}
