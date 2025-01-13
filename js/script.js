const header_title = document.querySelector('.header-title');



header_title.addEventListener('click', function() {
    window.location.reload();
});













function delegate(parent, type, selector, handler) {
    parent.addEventListener(type, function(event) {
        const targetElement = event.target.closest(selector);
        if (this.contains(targetElement)) handler.call(targetElement, event);
    });
}
