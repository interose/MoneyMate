import { Controller } from '@hotwired/stimulus';
import 'flowbite/dist/flowbite.turbo.min.js';

export default class extends Controller {

    static targets = ['output', 'mrOutputMonth', 'mrOutputYear', 'dropdown', 'monthOverviewCol', 'yearOverviewCol', 'form'];

    static values = {
        month: Number,
        year: Number,
        dropdownMonth: Number,
        dropdownYear: Number,
    }

    #monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
    #dropdown = null;

    connect() {
        this.#initDropdown();
    }

    prev() {
        if (1 == this.monthValue) {
            this.monthValue = 12;
            this.yearValue--;
        } else {
            this.monthValue--;
        }

        this.dropdownMonthValue = this.monthValue;
        this.dropdownYearValue = this.yearValue;
    }

    next() {
        if (12 == this.monthValue) {
            this.monthValue = 1;
            this.yearValue++;

        } else {
            this.monthValue++;
        }

        this.dropdownMonthValue = this.monthValue;
        this.dropdownYearValue = this.yearValue;
    }

    selectMonth(el) {
        this.dropdownMonthValue = el.target.dataset.value;
    }

    selectYear(el) {
        this.dropdownYearValue = el.target.innerText;
    }

    accept() {
        this.monthValue = this.dropdownMonthValue;
        this.yearValue = this.dropdownYearValue;

        this.#dropdown.hide();
    }

    cancel() {
        this.dropdownMonthValue = this.monthValue;
        this.dropdownYearValue = this.yearValue;

        this.#dropdown.hide();
    }

    monthValueChanged() {
        this.#updateInput();

        this.formTarget.requestSubmit();
    }
    yearValueChanged() {
        this.#updateInput();

        this.formTarget.requestSubmit();
    }
    dropdownMonthValueChanged(value) {
        this.#resetDropdownMonthCol();
        this.#setDropdownMonthCol();
    }
    dropdownYearValueChanged(value) {
        this.#resetDropdownYearCol();
        this.#setDropdownYearCol();
    }

    #initDropdown() {
        const options = {
            placement: 'bottom',
            triggerType: 'click',
            offsetSkidding: 0,
            offsetDistance: 10,
            delay: 300,
            ignoreClickOutsideClass: 'btn-monthpicker-navigate',
        };

        const instanceOptions = {
            id: 'monthpicker-dropdown',
            override: true
        };

        this.#dropdown = new Dropdown(this.dropdownTarget, this.outputTarget, options, instanceOptions);
    }

    #updateInput() {
        let monthName = this.#monthNames?.[this.monthValue - 1] ? this.#monthNames[this.monthValue - 1] : this.monthValue;

        this.outputTarget.value = `${monthName}, ${this.yearValue}`;
        this.mrOutputMonthTarget.value = this.monthValue;
        this.mrOutputYearTarget.value = this.yearValue;
    }

    #setDropdownMonthCol() {
        this.monthOverviewColTarget.querySelector(`div [data-value="${this.dropdownMonthValue}"]`).classList.add('bg-gray-100');
    }
    #resetDropdownMonthCol() {
        const list = this.monthOverviewColTarget.querySelectorAll('.bg-gray-100');
        for (const div of list) {
            div.classList.remove('bg-gray-100');
        }
    }

    #setDropdownYearCol() {
        this.yearOverviewColTarget.querySelector(`div [data-value="${this.dropdownYearValue}"]`).classList.add('bg-gray-100');
    }
    #resetDropdownYearCol() {
        const list = this.yearOverviewColTarget.querySelectorAll('.bg-gray-100');
        for (const div of list) {
            div.classList.remove('bg-gray-100');
        }
    }
}