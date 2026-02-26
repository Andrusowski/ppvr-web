<template>
    <button
        class="dark-mode-toggle uk-navbar-item"
        :class="{ 'uk-padding-remove': !isMobile }"
        @click="toggleDarkMode"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
        type="button"
    >
        <i v-if="isDark" class="fas fa-sun"></i>
        <i v-else class="fas fa-moon"></i>
    </button>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
    name: 'DarkModeToggle',
    props: {
        isMobile: {
            type: Boolean,
            default: false,
        },
    },
    setup() {
        const isDark = ref(false);
        const STORAGE_KEY = 'ppvr-dark-mode';

        function setTheme(dark) {
            isDark.value = dark;
            document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
            localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light');
        }

        function toggleDarkMode() {
            setTheme(!isDark.value);
        }

        function getSystemPreference() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        function initTheme() {
            const stored = localStorage.getItem(STORAGE_KEY);

            if (stored) {
                setTheme(stored === 'dark');
            } else {
                setTheme(getSystemPreference());
            }
        }

        onMounted(() => {
            initTheme();

            // Listen for system preference changes
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    // Only auto-switch if user hasn't set a manual preference
                    const stored = localStorage.getItem(STORAGE_KEY);
                    if (!stored) {
                        setTheme(e.matches);
                    }
                });
            }
        });

        return {
            isDark,
            toggleDarkMode
        };
    }
}
</script>
