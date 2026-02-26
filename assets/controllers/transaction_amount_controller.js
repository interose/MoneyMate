import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "differenceAmount" ]

    renderFinished({detail: {content}}) {
        this.differenceAmountTarget.innerText = content;
    }
}