import { Controller } from "@hotwired/stimulus"

export default class BaseDropdownController extends Controller {

    currentActiveIndex = -1;

    connect() {
        // Bind the hide method and add document-level click listener
        this.hideHandler = this.hide.bind(this)

        document.addEventListener("click", this.hideHandler)
    }

    disconnect() {
        // Clean up the listener when controller disconnects
        document.removeEventListener("click", this.hideHandler)
    }

    toggle(event) {
        const button = event.currentTarget;

        // If clicking the same button, toggle closed
        if (this.currentButton === button && !this.dropdownTarget.classList.contains("hidden")) {
            this.close();
            return;
        }

        // Store current button reference
        this.currentButton = button;

        // Reset state
        this.currentActiveIndex = -1;
        this.searchTarget.value = "";
        this.filterCategories({ target: this.searchTarget }); // Reset filter

        // Positioning relative to the anchor button
        const rect = button.getBoundingClientRect();
        this.dropdownTarget.style.top = `${rect.bottom + window.scrollY}px`;
        this.dropdownTarget.style.left = `${rect.left + window.scrollX}px`;
        this.dropdownTarget.style.width = `${rect.width}px`;
        this.dropdownTarget.classList.remove("hidden");
        this.dropdownTarget.dataset.transactionId = button.dataset.transactionId;

        setTimeout(() => {
            this.searchTarget.focus();
            this.searchTarget.select();
        }, 10);
    }

    handleKeyNavigation(event) {
        if (event.key === 'Escape') {
            return; // Let handleEscape deal with it
        }
        const visibleItems = Array.from(this.catItemTargets).filter(item => item.style.display !== 'none');

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.currentActiveIndex = Math.min(this.currentActiveIndex + 1, visibleItems.length - 1);
            this.updateHighlight(visibleItems);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.currentActiveIndex = Math.max(this.currentActiveIndex - 1, 0);
            this.updateHighlight(visibleItems);
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (this.currentActiveIndex >= 0 && visibleItems[this.currentActiveIndex]) {
                visibleItems[this.currentActiveIndex].click();
            }
        } else {
            this.currentActiveIndex = -1;
            this.filterCategories(event);
        }
    }

    updateHighlight(visibleItems) {
        visibleItems.forEach((item, idx) => {
            if (idx === this.currentActiveIndex) {
                item.classList.add('bg-active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-active');
            }
        });
    }

    handleEscape(event) {
        if (event.key === 'Escape' && !this.dropdownTarget.classList.contains('hidden')) {
            event.preventDefault();
            event.stopPropagation();

            // Clear search input and close
            this.searchTarget.value = '';
            this.filterCategories({ target: this.searchTarget }); // Reset filter
            this.close();
        }
    }

    close() {
        this.dropdownTarget.classList.add("hidden")
        this.currentButton = null
    }

    hide(event) {
        // Close if clicking outside
        if (!this.dropdownTarget.contains(event.target) &&
            !this.currentButton?.contains(event.target)) {
            this.close()
        }
    }

    filterCategories(event) {
        const filter = event.target.value.toLowerCase();

        this.catItemTargets.forEach(item => {
            if (item.textContent.toLowerCase().includes(filter)) {
                item.style.display = "";
                // hasVisibleChild = true;
            } else {
                item.style.display = "none";
                item.classList.remove('bg-active');
            }
        });
    }
}