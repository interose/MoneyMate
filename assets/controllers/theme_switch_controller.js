import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["pill", "label"];
    static values = {
        storageKey: { type: String, default: "app-theme" }
    };

    connect() {
        const savedTheme = localStorage.getItem(this.storageKeyValue);
        const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

        const initialTheme = savedTheme || (prefersDark ? "dark" : "light");
        this.applyTheme(initialTheme);
    }

    toggle() {
        const currentTheme = document.documentElement.getAttribute("data-theme") || "dark";
        const nextTheme = currentTheme === "light" ? "dark" : "light";

        this.applyTheme(nextTheme);
    }

    applyTheme(theme) {
        const root = document.documentElement;
        root.setAttribute("data-theme", theme);

        localStorage.setItem(this.storageKeyValue, theme);

        const isDark = theme === "dark";
        this.pillTarget.classList.toggle('on', !isDark);
        this.labelTarget.textContent = isDark ? 'Light mode' : 'Dark mode';
    }
}
