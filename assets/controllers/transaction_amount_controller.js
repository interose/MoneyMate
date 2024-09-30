import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "differenceAmount" ]

    renderFinished({detail: {content}}) {
        // transform the value into a currency value
        let amount = content / 100;
        this.differenceAmountTarget.innerText = amount.toFixed(2).replace('.', ',') + ' €';
    }
}