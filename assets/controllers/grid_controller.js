import { Controller } from '@hotwired/stimulus';
import { computePosition, flip, offset } from '@floating-ui/dom';
import { renderStreamMessage } from "@hotwired/turbo";

export default class extends Controller {

    static targets = ['dropdown', 'dropdownTransactionValue', 'dropdownForm', 'dropdownSelect'];

    showDropdown(event) {
        const tooltip = this.dropdownTarget;
        this.dropdownTransactionValueTarget.value = event.target.dataset.transactionId;

        this.dropdownSelectTarget.value = event.target.dataset.transactionCategoryId;

        tooltip.classList.remove('hidden');
        computePosition(event.target, tooltip, {
            middleware: [flip(), offset({mainAxis: -25, crossAxis: -24})],
        }).then(({x, y}) => {
            Object.assign(tooltip.style, {
                left: `${x}px`,
                top: `${y}px`,
            });
        });
    }

    hide(event) {
        const target = event.target;

        // do nothing if,
        // ... dropdown is not visible
        // ... select within dropdow is NOT clicked
        // ... column with categories is NOT clicked
        if (this.dropdownTarget.classList.contains('hidden')) { return }
        if (target.hasAttribute('data-action')) { return }
        if (target.tagName === 'SELECT') { return }
        if (target.tagName === 'OPTION') { return }

        this.dropdownTarget.classList.add('hidden');
    }

    async submit(event) {
        let url = this.dropdownFormTarget.getAttribute('action');
        let formData = new FormData(this.dropdownFormTarget);


        let result = await fetch(url, {
            method: 'POST',
            body: formData
        });

        renderStreamMessage(await result.text());

        this.dropdownTarget.classList.add('hidden');
    }
}