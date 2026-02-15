import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static outlets = ["ta-dropdown-manager"]

    toggle(event) {
        event.stopPropagation()

        if (this.hasTaDropdownManagerOutlet) {
            this.taDropdownManagerOutlet.showMenu(this.element)
        }
    }
}