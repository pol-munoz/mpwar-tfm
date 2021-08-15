import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "nav", "icon" ]

    initialize() {
        this.children = this.navTarget.getElementsByClassName('App-nav-item')
        this.isOpen = (localStorage.getItem('openSidebar') ?? 'true') === 'true'
        this.renderSidebar()
        window.setTimeout(() => {
            this.children.forEach(item => item.classList.add('App-nav-item-animated'))
        }, 0)
    }

    toggle() {
        this.isOpen = !this.isOpen
        this.renderSidebar()
        localStorage.setItem('openSidebar', this.isOpen)
    }

    renderSidebar() {
        if (this.isOpen) {
            this.children.forEach(item => item.style = "width: 150px")
            this.iconTarget.classList.replace('fa-angle-double-right', 'fa-angle-double-left')
        } else {
            this.children.forEach(item => item.style = "")
            this.iconTarget.classList.replace('fa-angle-double-left', 'fa-angle-double-right')
        }
    }
}
