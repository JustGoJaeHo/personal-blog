(function () {
    var toast = document.querySelector('[data-flash-toast]');

    if (!toast) {
        return;
    }

    var displayMs = 2000;
    var fadeMs = 300;

    requestAnimationFrame(function () {
        toast.classList.add('is-visible');
    });

    setTimeout(function () {
        toast.classList.remove('is-visible');
        setTimeout(function () {
            toast.remove();
        }, fadeMs);
    }, displayMs);
})();
