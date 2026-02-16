import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["menu", "search", "group", "catItem"]

    currentActiveIndex = -1;

    showMenu(buttonElement) {
        // If clicking the same button, toggle closed
        if (this.currentButton === buttonElement && !this.menuTarget.classList.contains("hidden")) {
            this.close()
            return
        }

        // Store current button reference
        this.currentButton = buttonElement

        // Reset state
        this.currentActiveIndex = -1;
        this.searchTarget.value = '';
        this.filterCategories({ target: this.searchTarget }); // Reset filter

        // Positioning relative to the anchor button
        const rect = buttonElement.getBoundingClientRect();
        this.menuTarget.style.top = `${rect.bottom + window.scrollY}px`
        this.menuTarget.style.left = `${rect.left + window.scrollX}px`
        this.menuTarget.style.width = `${rect.width}px`;
        this.menuTarget.classList.remove("hidden")

        setTimeout(() => {
            this.searchTarget.focus();
            this.searchTarget.select();
        }, 10);
    }

    close() {
        this.menuTarget.classList.add("hidden")
        this.currentButton = null
    }

    hide(event) {
        // Close if clicking outside
        if (!this.menuTarget.contains(event.target) &&
            !this.currentButton?.contains(event.target)) {
            this.close()
        }
    }

    handleKeyNavigation(event) {
        if (event.key === 'Escape') {
            return; // Let handleEscape deal with it
        }
        const list = document.getElementById('dropdown-list');
        const visibleItems = Array.from(list.querySelectorAll('.cat-item')).filter(item => item.style.display !== 'none');

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
            // if (currentActiveIndex >= 0 && visibleItems[currentActiveIndex]) {
            //     visibleItems[currentActiveIndex].click();
            // }
        } else {
            // currentActiveIndex = -1;
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
        if (event.key === 'Escape' && !this.menuTarget.classList.contains('hidden')) {
            event.preventDefault();
            event.stopPropagation();

            // Clear search input and close
            this.searchTarget.value = '';
            this.filterCategories({ target: this.searchTarget }); // Reset filter
            this.close();
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

        // this.groupTargets.forEach(group => {
        //     let hasVisibleChild = false;
        //     const catItems = group.querySelectorAll('.cat-item');
        //
        //     catItems.forEach(item => {
        //         if (item.textContent.toLowerCase().includes(filter)) {
        //             item.style.display = "";
        //             hasVisibleChild = true;
        //         } else {
        //             item.style.display = "none";
        //             item.classList.remove('bg-active');
        //         }
        //     });
        //
        //     group.style.display = hasVisibleChild ? "" : "none";
        // });
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