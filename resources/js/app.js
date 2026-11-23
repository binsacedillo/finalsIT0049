import './bootstrap';

const themeOptions = ['light', 'dark', 'system'];
const themeMedia = window.matchMedia('(prefers-color-scheme: dark)');

function getThemePreference() {
    return document.documentElement.dataset.themePreference || 'system';
}

function applyTheme(preference) {
    const resolvedTheme = preference === 'system'
        ? (themeMedia.matches ? 'dark' : 'light')
        : preference;

    document.documentElement.dataset.theme = resolvedTheme;
    document.documentElement.dataset.themePreference = preference;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = button.querySelector('[data-theme-label]');
        const lightIcon = button.querySelector('[data-theme-icon="light"]');
        const darkIcon = button.querySelector('[data-theme-icon="dark"]');
        const isStaffLayout = Boolean(button.closest('header'));

        if (label) {
            label.textContent = preference === 'system'
                ? (isStaffLayout ? 'System' : 'System theme')
                : preference === 'dark' ? 'Dark' : 'Light';
        }

        button.setAttribute('aria-label', `Color theme: ${preference}. Activate to change theme`);
        lightIcon?.classList.toggle('hidden', resolvedTheme !== 'light');
        darkIcon?.classList.toggle('hidden', resolvedTheme !== 'dark');
    });
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const currentPreference = getThemePreference();
        const nextPreference = themeOptions[(themeOptions.indexOf(currentPreference) + 1) % themeOptions.length];

        try {
            localStorage.setItem('pos-theme', nextPreference);
        } catch (error) {
            // Keep the selection for this page view even when storage is unavailable.
        }

        applyTheme(nextPreference);
    });
});

themeMedia.addEventListener('change', () => {
    if (getThemePreference() === 'system') {
        applyTheme('system');
    }
});

applyTheme(getThemePreference());

const menuButton = document.querySelector('#mobile-menu-toggle');
const navigation = document.querySelector('#primary-navigation');

if (menuButton && navigation) {
    navigation.classList.add('hidden');

    const openMenu = (open) => {
        navigation.classList.toggle('hidden', !open);
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
        menuButton.querySelector('[data-menu-icon="open"]')?.classList.toggle('hidden', open);
        menuButton.querySelector('[data-menu-icon="close"]')?.classList.toggle('hidden', !open);

        if (open) {
            navigation.querySelector('a')?.focus();
        }
    };

    menuButton.addEventListener('click', () => {
        openMenu(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    navigation.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            openMenu(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
            openMenu(false);
            menuButton.focus();
        }
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) {
            openMenu(false);
        }
    });
}
