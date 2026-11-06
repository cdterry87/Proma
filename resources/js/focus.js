// A browser moves focus to the body when the focused element is removed or
// disabled, which sends a keyboard user back to the top of the page. Livewire
// does both: it disables a form's controls while saving, and a response can
// replace what was focused.
function restoreFocus(component, hadFocus, submitted) {
    if (submitted) {
        const root = document.querySelector('dialog[open]') ?? component.el;
        const invalid = root.querySelector('[aria-invalid="true"]');

        if (invalid) {
            invalid.focus();

            return;
        }
    }

    if (document.activeElement !== document.body || !(hadFocus || submitted)) {
        return;
    }

    const target = component.el.querySelector('[data-focus-target]') ?? document.getElementById('main');

    target?.focus({ preventScroll: true });
}

// Moving to the heading tells a screen reader that the page changed, as a full
// page load would.
function focusPageHeading() {
    const heading = document.querySelector('h1');

    if (heading) {
        heading.tabIndex = -1;
        heading.focus({ preventScroll: true });
    }
}

export function registerFocusManagement() {
    let submitted = false;
    let navigating = false;

    document.addEventListener('submit', () => {
        submitted = true;
    }, true);

    document.addEventListener('livewire:init', () => {
        window.Livewire.hook('commit', ({ component, succeed }) => {
            const hadFocus = document.activeElement !== document.body;

            succeed(() => {
                requestAnimationFrame(() => {
                    restoreFocus(component, hadFocus, submitted);
                    submitted = false;
                });
            });
        });
    });

    document.addEventListener('livewire:navigating', () => {
        navigating = true;
    });

    document.addEventListener('livewire:navigated', () => {
        if (navigating) {
            navigating = false;
            focusPageHeading();
        }
    });
}
