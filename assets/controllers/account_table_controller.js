import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    toggle(event) {
        event.preventDefault();

        event.currentTarget.classList.toggle('selected');

        let target = document.getElementById(event.params.target);
        target.classList.toggle('hidden');

        return false;
    }
}