export function addGlobalEventListener(type, selector, callback, parent = document) {
    parent.addEventListener(type, e => {
        const target = e.target.closest(selector);
        if (target) {
            callback(e, target);
        }
    });
}
