import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['groupLink'];

    connect() {
    }

    disconnect() {
    }

    toggle(event) {
        this.groupLinkTargets.forEach(link => {
            link.classList.remove('selected');
        })
        event.currentTarget.classList.add('selected')
    }
}
