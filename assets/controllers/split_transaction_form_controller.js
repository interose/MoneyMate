import { getComponent } from '@symfony/ux-live-component';
import { computePosition, arrow, offset } from '@floating-ui/dom';
import BaseDropdownController from "./base_dropdown_controller.js"

export default class extends BaseDropdownController {

    static targets = ["dropdown", "search", "group", "list", "catItem"]

    static values = {
        transactionAmount: Number
    }

    connect() {
        super.connect();
        const _self = this;

        let errorElements = _self.element.querySelectorAll('div[id$="_error"]');
        if (errorElements.length === 0) {
            return;
        }

        this._renderError(errorElements);
    }

    disconnect() {
        super.disconnect();

        if (this.component) {
            this.component.off("render:finished", this.liveUpdatedHandler);
        }
    }

    async initialize() {
        this.component = await getComponent(this.element);

        this.component.on("render:finished", () => {
            this._initializeCategoryLabels();
            this._updateDifferenceAmount();
        });

        this._initializeCategoryLabels();
    }

    _onLiveUpdated() {
        this._initializeCategoryLabels();
    }

    _renderError(errors) {
        const _self = this;
        for(const div of errors) {
            const id = div.getAttribute('id').replace('_error', '');

            const target = _self.element.querySelector(`#${id}`);
            const arrowEl = div.querySelector('.tooltip-arrow');
            const arrowLen = arrowEl.offsetWidth;

            div.classList.remove('invisible', 'opacity-0');

            computePosition(target, div, {
                placement: 'top-center',
                middleware: [offset(10), arrow({ element: arrowEl })]
            }).then(({x, y, middlewareData, placement}) => {
                Object.assign(div.style, {
                    left: `${x}px`,
                    top: `${y}px`,
                });

                const side = placement.split("-")[0];
                const staticSide = {
                    top: "bottom",
                    right: "left",
                    bottom: "top",
                    left: "right"
                }[side];

                if (middlewareData.arrow) {
                    const { x, y } = middlewareData.arrow;
                    Object.assign(arrowEl.style, {
                        left: x != null ? `${x}px` : "",
                        top: y != null ? `${y}px` : "",
                        // Ensure the static side gets unset when
                        // flipping to other placements' axes.
                        right: "",
                        bottom: "",
                        [staticSide]: `${-arrowLen / 2}px`,
                    });
                }
            });
        }
    }

    selectCategory(event) {
        const button = event.currentTarget;
        const categoryId = button.dataset.categoryId;
        const categoryName = button.textContent.trim();

        // Find the hidden field for the current row
        const rowIndex = this.currentButton?.dataset.rowIndex;
        if (rowIndex === undefined) return;

        const hiddenField = document.querySelector(
            `input[name="transaction[splitTransactions][${rowIndex}][category]"]`
        );

        if (hiddenField) {
            hiddenField.value = categoryId;
            // Dispatch a change event so the Live Component syncs the value
            hiddenField.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Update the label on the trigger button
        const label = this.currentButton.querySelector("span");
        if (label) {
            label.textContent = categoryName;
        }

        this.close();
    }

    _initializeCategoryLabels() {
        const hiddenFields = this.element.querySelectorAll(
            'input[type="hidden"][name*="[category]"]'
        );

        hiddenFields.forEach((field) => {
            if (!field.value) return;

            // Extract row index from name e.g. transaction[splitTransactions][0][category]
            const match = field.name.match(/\[splitTransactions\]\[(\d+)\]\[category\]/);
            if (!match) return;

            const rowIndex = match[1];
            const categoryId = field.value;

            // Find the trigger button for this row
            const button = this.element.querySelector(
                `[data-row-index="${rowIndex}"]`
            );
            if (!button) return;

            // Find the matching cat-item to get the category name
            const catItem = this.element.querySelector(
                `[data-category-id="${categoryId}"]`
            );

            if (catItem) {
                const label = button.querySelector("span");
                if (label) label.textContent = catItem.textContent.trim();
            }
        });
    }

    _updateDifferenceAmount() {
        const inputs = this.component.element.querySelectorAll('input[name$="[amount]"]');

        let differenceAmount = this.transactionAmountValue;
        for (const input of inputs) {
            const amount = parseFloat(input.value.replace(',', '.')) || 0;
            differenceAmount -= amount * 100;
        }

        // Format and update the div directly
        const formatted = new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR'
        }).format(differenceAmount / 100);

        this.dispatch("render:finished", {detail: { content: formatted }});
    }
}