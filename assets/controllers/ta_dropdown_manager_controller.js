import BaseDropdownController from "./base_dropdown_controller.js"

export default class extends BaseDropdownController {
    static targets = ["dropdown", "search", "group", "list", "catItem"]

    static values = {
        updateUrl: String
    }

    async selectCategory(event) {
        const button = event.currentTarget;
        const categoryId = button.dataset.categoryId;
        const transactionId = this.dropdownTarget.dataset.transactionId;
        const splitTransactionId = this.dropdownTarget.dataset.splitTransactionId ?? 0;
        const splitFirst = this.dropdownTarget.dataset.splitFirst ?? false;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.updateUrlValue;

        const fields = {
            transactionId: transactionId,
            categoryId: categoryId,
            splitTransactionId: splitTransactionId,
            splitFirst: splitFirst
        };

        for (const [key, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        Turbo.navigator.submitForm(form);
        document.body.removeChild(form);

        this.close();
    }
}
