import { ref } from 'vue';

const storageKey = 'valtrix-theme';
const isDark = ref(false);
let initialized = false;

function applyTheme(dark) {
    const theme = dark ? 'dark' : 'light';

    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;

    const themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) {
        themeColor.setAttribute('content', dark ? '#0c1320' : '#2148e8');
    }
}

export function initializeTheme() {
    if (initialized) {
        return;
    }

    isDark.value = window.localStorage.getItem(storageKey) === 'dark';
    applyTheme(isDark.value);
    initialized = true;
}

export function useTheme() {
    initializeTheme();

    function toggleTheme() {
        isDark.value = !isDark.value;
        applyTheme(isDark.value);
        window.localStorage.setItem(storageKey, isDark.value ? 'dark' : 'light');
    }

    return { isDark, toggleTheme };
}
