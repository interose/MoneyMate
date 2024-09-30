import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
import { computePosition, arrow, offset } from '@floating-ui/dom';

export default class extends Controller {

    static values = {
        transactionAmount: Number
    }

    connect() {
        const _self = this;

        let errorElements = _self.element.querySelectorAll('div[id$="_error"]');
        if (errorElements.length === 0) {
            return;
        }

        for(const div of errorElements) {
            const id = div.getAttribute('id').replace('_error', '');

            const target = _self.element.querySelector(`#${id}`);
            const arrowEl = div.querySelector('.tooltip-arrow');
            const arrowLen = arrowEl.offsetWidth;

            div.classList.remove('invisible', 'opacity-0');

            computePosition(target, div, {
                placement: 'top-center',
                middleware: [offset(10), arrow({ element: arrowEl })]
            }).then(({x, y, middlewareData, placement}) => {
                Object.assign(div.style, {
                    left: `${x}px`,
                    top: `${y}px`,
                });

                const side = placement.split("-")[0];
                const staticSide = {
                    top: "bottom",
                    right: "left",
                    bottom: "top",
                    left: "right"
                }[side];

                if (middlewareData.arrow) {
                    const { x, y } = middlewareData.arrow;
                    Object.assign(arrowEl.style, {
                        left: x != null ? `${x}px` : "",
                        top: y != null ? `${y}px` : "",
                        // Ensure the static side gets unset when
                        // flipping to other placements' axes.
                        right: "",
                        bottom: "",
                        [staticSide]: `${-arrowLen / 2}px`,
                    });
                }
            });
        }
    }

    async initialize() {
        const _self = this;

        _self.component = await getComponent(_self.element);

        _self.component.on('render:finished', (component) => {
            let inputList = component.element.querySelectorAll('input[name$="[amount]"]');

            let differenceAmount = this.transactionAmountValue;

            for(const input of inputList) {
                let amount = input.value.replace(',', '.');
                differenceAmount -= parseFloat(amount) * 100;
            }

            _self.dispatch("render:finished", {detail: { content: differenceAmount }});
        });
    }
}