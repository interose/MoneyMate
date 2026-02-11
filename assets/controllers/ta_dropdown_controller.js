import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = [ "dropdown", "search" ]


    toggle(event) {
        event.stopPropagation()

        // Dispatch event to close other dropdowns before toggling this one
        if (this.dropdownTarget.classList.contains("hidden")) {
            console.log('dispatch');
            this.dispatch("opening", { detail: { controller: this } })
        }

        const isOpening = this.dropdownTarget.classList.contains('hidden');
        this.dropdownTarget.classList.toggle('hidden');

        if (isOpening) {
            setTimeout(() => {
                this.searchTarget.focus();
                this.searchTarget.select();
            }, 10);
        }
    }

    close() {
        this.dropdownTarget.classList.add("hidden")
    }

    hide(event) {
        // Close dropdown if click is outside the controller element
        if (!this.element.contains(event.target)) {
            this.close()
        }
    }

    connect() {
        // Bind the hide method and add document-level click listener
        this.hideHandler = this.hide.bind(this)
        document.addEventListener("click", this.hideHandler)
    }

    disconnect() {
        // Clean up the listener when controller disconnects
        document.removeEventListener("click", this.hideHandler)
    }
}