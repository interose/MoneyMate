import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ["trendList", "commentList"];

    initialize() {
        this.#resetFilter();
    }

    submit(e) {
        const _self = this;

        this.dispatch("stockFilterChanged", {
            detail: {data: new FormData(_self.element)}
        });
    }

    filterItemDeleted(e) {
        const type = e.detail?.data?.type ?? '';
        const value = e.detail?.data?.value ?? '';

        if (type.length === 0 || value.length === 0) {
            return;
        }

        let list = null;
        if ('trend' === type) {
            list = this.trendListTarget;
        } else if ('comment' === type) {
            list = this.commentListTarget;
        } else {
            return;
        }

        if (null === list) {
            return;
        }

        let inputs = list.querySelectorAll('input');
        for (const checkbox of inputs) {
            if (value === checkbox.value) {
                checkbox.checked = false;
            }
        }

        this.submit();
    }

    #resetFilter() {
        this.#resetTrendFilter();
        this.#resetCommentFilter();
    }

    #resetTrendFilter() {
        let list = this.trendListTarget;
        let inputs = list.querySelectorAll('input');

        for (const checkbox of inputs) {
            checkbox.checked = false;
        }
    }

    #resetCommentFilter() {
        let list = this.commentListTarget;
        let inputs = list.querySelectorAll('input');

        for (const checkbox of inputs) {
            checkbox.checked = false;
        }
    }
}