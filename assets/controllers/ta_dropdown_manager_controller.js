import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    closeAll(event) {
        const openingController = event.detail.controller

        // Find all dropdown controllers and close them except the one opening
        this.element.querySelectorAll('[data-controller~="ta-dropdown"]').forEach(element => {
            const controller = this.application.getControllerForElementAndIdentifier(element, "ta-dropdown")
            if (controller && controller !== openingController) {
                controller.close()
            }
        })
    }
}