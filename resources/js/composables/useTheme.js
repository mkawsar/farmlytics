import { ref, onMounted } from 'vue';

const THEME_KEY = 'farmlytics-theme';

export function useTheme() {
    const isDark = ref(false);

    function applyTheme(dark) {
        isDark.value = dark;
        if (typeof document !== 'undefined') {
            if (dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            try {
                localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light');
            } catch (_) {}
        }
    }

    function toggleTheme() {
        applyTheme(!isDark.value);
    }

    onMounted(() => {
        isDark.value = document.documentElement.classList.contains('dark');
    });

    return { isDark, toggleTheme };
}
