/**
 * Listens on a global target for as long as an element stays in the document.
 * Elements swapped out by wire:navigate remove their own listener this way.
 */
export function listenWhileConnected(element, target, event, handler) {
    const listener = (payload) => {
        if (!element.isConnected) {
            target.removeEventListener(event, listener);

            return;
        }

        handler(payload);
    };

    target.addEventListener(event, listener);
}
