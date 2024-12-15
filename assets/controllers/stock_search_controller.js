import { Controller } from "@hotwired/stimulus"
import debounce from 'debounce'

export default class extends Controller {

    static targets = ["filterContainer", "inputQuery", "btnReset", "btnShortcut"];

    initialize() {
        this.debouncedSubmit = debounce(this.debouncedSubmit.bind(this), 300)
    }

    connect() {
        this.#resetSearchQuery();
    }


    focusSearch(event) {
        event.preventDefault();

        this.inputQueryTarget.focus();
    }

    filterChanged(e) {
        this.#resetFilterContainer();

        const container = this.filterContainerTarget;
        let input = '';
        for (const p of e.detail.data) {
            input = document.createElement('input');
            input.setAttribute('type', 'hiddden');
            input.setAttribute('name', p[0]);
            input.setAttribute('value', p[1]);

            container.appendChild(input);
        }

        this.element.requestSubmit();
    }

    submit(e) {
        this.element.requestSubmit();
    }

    debouncedSubmit() {
        this.btnResetTarget.classList.remove('hidden');
        this.btnShortcutTarget  .classList.add('hidden');

        this.submit()
    }

    clearQuery() {
        this.#resetSearchQuery();
        this.submit();
    }

    #resetFilterContainer() {
        const container = this.filterContainerTarget;

        while(container.firstChild) {
            container.removeChild(container.firstChild);
        }
    }

    #resetSearchQuery() {
        this.inputQueryTarget.value = "";

        this.btnResetTarget.classList.add('hidden');
        this.btnShortcutTarget.classList.remove('hidden');
    }
}