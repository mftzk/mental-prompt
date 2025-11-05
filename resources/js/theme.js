/**
 * Theme Management System
 * Auto-detects system preference, persists to localStorage, and manages theme switching
 */

class ThemeManager {
    constructor() {
        this.init();
    }

    /**
     * Initialize theme on page load
     * Priority: localStorage > system preference > default 'dark'
     */
    init() {
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        let themeToApply;

        if (savedTheme) {
            // Use saved preference
            themeToApply = savedTheme;
        } else {
            // Auto-detect from system preference
            themeToApply = systemPrefersDark ? 'dark' : 'light';
            // Save the auto-detected preference
            localStorage.setItem('theme', themeToApply);
        }

        this.applyTheme(themeToApply);

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            if (!localStorage.getItem('theme')) {
                const newTheme = e.matches ? 'dark' : 'light';
                this.applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            }
        });
    }

    /**
     * Apply theme to document
     * @param {string} theme - 'light' or 'dark'
     */
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }

    /**
     * Toggle between light and dark themes
     */
    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        this.applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);

        return newTheme;
    }

    /**
     * Get current theme
     * @returns {string} - 'light' or 'dark'
     */
    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'dark';
    }

    /**
     * Set theme manually
     * @param {string} theme - 'light' or 'dark'
     */
    setTheme(theme) {
        this.applyTheme(theme);
        localStorage.setItem('theme', theme);
    }
}

// Initialize theme manager when DOM is ready
let themeManager;
document.addEventListener('DOMContentLoaded', () => {
    themeManager = new ThemeManager();
});

// Export for global use
window.ThemeManager = ThemeManager;
window.themeManager = themeManager;
