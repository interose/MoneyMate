import { Controller } from '@hotwired/stimulus';
import 'flowbite/dist/flowbite.turbo.min.js'

export default class extends Controller {
    static targets = ['dialog', 'dynamicContent', 'loadingContent'];

    observer = null;

    connect() {
        const $targetEl = document.getElementById('drawer-right-example');
        const options = {
            placement: 'right',
            backdrop: true,
            bodyScrolling: false,
            edge: false,
            edgeOffset: '',
            backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-30',
        };
        const instanceOptions = {
            id: 'drawer-js-example',
            override: true
        };
        this.drawer = new Drawer($targetEl, options, instanceOptions);


        if (this.hasDynamicContentTarget) {
            // when the content changes, call this.open()
            this.observer = new MutationObserver(() => {
                const shouldOpen = this.dynamicContentTarget.innerHTML.trim().length > 0;

                if (shouldOpen && !this.drawer.isVisible()) {
                    this.open();
                } else if (!shouldOpen && this.drawer.isVisible()) {
                    this.close();
                }
            });
            this.observer.observe(this.dynamicContentTarget, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
    }

    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
        }
        if (this.drawer.isVisible()) {
            this.close();
        }
    }

    open() {
        this.drawer.show();
    }

    close() {
        this.drawer.hide();
    }

    clickOutside(event) {
        if (event.target !== this.drawer) {
            return;
        }

        if (!this.#isClickInElement(event, this.drawer)) {
            this.drawer.hide();
        }
    }

    showLoading() {
        // do nothing if the dialog is already open
        if (this.drawer.isVisible()) {
            return;
        }

        this.dynamicContentTarget.innerHTML = this.loadingContentTarget.innerHTML;
    }

    #isClickInElement(event, element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= event.clientY &&
            event.clientY <= rect.top + rect.height &&
            rect.left <= event.clientX &&
            event.clientX <= rect.left + rect.width
        );
    }
}
