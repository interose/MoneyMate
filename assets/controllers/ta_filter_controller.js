import { Controller } from '@hotwired/stimulus';
import debounce from 'debounce';

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

export default class extends Controller {

    static targets = [
        'form',
        'month', 'year', 'pickerLabel', 'pickerDropdown', 'pickerChevron', 'dropdownYearLabel', 'dropdownMonths',
        'searchQuery'
    ];

    month = null;
    year = null;
    dropdownOpen = false;
    dropdownYear = null;

    // -------------------------
    // Lifecycle
    // -------------------------
    initialize() {
        this.debouncedSubmit = debounce(this.debouncedSubmit.bind(this), 500);
    }

    connect() {
        this.month = parseInt(this.monthTarget.value);
        this.year = parseInt(this.yearTarget.value);
    }

    // -------------------------
    // Month Picker Actions
    // -------------------------
    prevMonth(e) {
        this.#monthStep(-1);
        this.#updatePickerLabel();
        this.#updateHiddenFields();
    }

    nextMonth(e) {
        this.#monthStep(1);
        this.#updatePickerLabel();
        this.#updateHiddenFields();
    }

    pickerPrevYear() {
        this.dropdownYear -=1;
        this.#renderDropdown();
    }

    pickerNextYear() {
        this.dropdownYear +=1;
        this.#renderDropdown();
    }

    togglePickerDropdown() {
        this.dropdownOpen ? this.#closePickerDropdown() : this.#openPickerDropdown();
    }

    dropdownSelectMonth(el) {
        this.month = parseInt(el.target.dataset.value);
        this.year = this.dropdownYear;

        this.#updatePickerLabel();
        this.#updateHiddenFields();
        this.#closePickerDropdown();
    }

    // -------------------------
    // Search Actions
    // -------------------------
    focusSearch(event) {
        event.preventDefault();
        this.searchQueryTarget.focus();
    }
    clearSearchQuery() {
        this.#resetSearchQuery();
    }

    debouncedSubmit() {
        // this.searchQueryResetTarget.classList.remove('hidden');
        // this.searchQueryShortcutTarget.classList.add('hidden');

        this.formTarget.requestSubmit();
    }


    // -------------------------
    // Month Picker Private
    // -------------------------
    #monthStep(dir) {
        this.month += dir;
        if (this.month > 12) { this.month = 1;  this.year++; }
        if (this.month < 1)  { this.month = 12; this.year--; }
    }

    #updatePickerLabel() {
        this.pickerLabelTarget.innerText = `${MONTHS[this.month - 1]} ${this.year}`;
    }

    #updateHiddenFields() {
        this.monthTarget.value = this.month;
        this.yearTarget.value = this.year;
    }

    #openPickerDropdown() {
        this.dropdownOpen  = true;
        this.dropdownYear = this.year

        this.#renderDropdown();

        this.pickerDropdownTarget.classList.remove('hidden');
        this.pickerChevronTarget.style.transform = 'rotate(180deg)';
    }

    #closePickerDropdown() {
        this.dropdownOpen  = false;

        this.pickerDropdownTarget.classList.add('hidden');
        this.pickerChevronTarget.style.transform = '';
    }

    #renderDropdown() {
        this.dropdownYearLabelTarget.innerText = this.dropdownYear;

        this.dropdownMonthsTarget
            .querySelectorAll('button')
            .forEach(btn => {
                const isActive  = (this.dropdownYear === this.year && btn.dataset.value == this.month);
                if (isActive) {
                    btn.classList.replace('text-white/25', 'text-white');
                } else {
                    btn.classList.remove('text-white')
                    btn.classList.add('text-white/25')
                }
            });
    }

    // -------------------------
    // Search Private
    // -------------------------
    #resetSearchQuery() {
        this.searchQueryTarget.value = "";
        this.searchQueryResetTarget.classList.add('hidden');
        this.searchQueryShortcutTarget.classList.remove('hidden');
    }
}
