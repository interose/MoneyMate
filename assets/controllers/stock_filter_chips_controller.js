import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["chip"]
    static classes = ["active", "inactive"];

    toggle(event) {
        event.preventDefault()
        const clickedChip = event.currentTarget

        // Extract the specific filter value this chip represents from its href
        const chipUrl = new URL(clickedChip.href, window.location.origin)
        const filterValue = chipUrl.searchParams.get("filter[comment][0]")

        // Get the current state of the Turbo Frame
        const frame = document.getElementById("stock-list")
        const currentUrl = new URL(frame.src || window.location.href, window.location.origin)
        const params = currentUrl.searchParams

        // Collect all currently active comment filters from the URL
        let activeFilters = []
        for (let [key, value] of params.entries()) {
            if (key.includes("filter[comment]")) {
                activeFilters.push(value)
            }
        }

        if (activeFilters.includes(filterValue)) {
            // STATE 1: REMOVE - Value exists, so we toggle it off
            activeFilters = activeFilters.filter(v => v !== filterValue)
            clickedChip.classList.remove(...this.activeClasses)
        } else {
            // STATE 2: ADD - Value is new, so we add it to the selection
            activeFilters.push(filterValue)
            clickedChip.classList.add(...this.activeClasses)
        }

        // STATE 3: REBUILD URL
        // First, remove all existing comment filter keys to avoid duplicates or index gaps
        const keysToDelete = []
        for (let key of params.keys()) {
            if (key.includes("filter[comment]")) keysToDelete.push(key)
        }
        keysToDelete.forEach(k => params.delete(k))

        // Re-add the remaining/new active filters using indexed keys
        activeFilters.forEach((val, index) => {
            params.append(`filter[comment][${index}]`, val)
        })

        // Update the frame source with the new URL (preserving sorting, pagination, etc.)
        if (frame) {
            frame.src = `${currentUrl.pathname}?${params.toString()}`
        }
    }
}
