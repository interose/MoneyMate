import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "template", "content"];

    filterItemDeleted(e) {
        this.dispatch('filterItemDeleted', {
            detail: {data: {
                'type': e.currentTarget.dataset.filterType,
                'value': e.currentTarget.dataset.filterValue
            }}
        });
    }

    filterChanged(e) {
        const _self = this;

        _self.contentTarget.innerHTML = "";

        const formData = e.detail.data;
        formData.getAll('filter[trend][]').forEach(function(filter) {
            let clone = _self.templateTarget.content.cloneNode(true);
            clone.querySelector('.filter-name').textContent = 'Trend = ' +filter;
            clone.querySelector('button').dataset.filterType = 'trend';
            clone.querySelector('button').dataset.filterValue = filter;
            _self.contentTarget.appendChild(clone);
        });
        formData.getAll('filter[comment][]').forEach(function(filter) {
            let clone = _self.templateTarget.content.cloneNode(true);
            clone.querySelector('.filter-name').textContent = 'Comment = ' +filter;
            clone.querySelector('button').dataset.filterType = 'comment';
            clone.querySelector('button').dataset.filterValue = filter;
            _self.contentTarget.appendChild(clone);
        });
    }

}