document.addEventListener('DOMContentLoaded', function() {
    var toast = document.getElementById('regnot');
    if (toast) {
        setTimeout(function() {
            toast.classList.add('hide');
        }, 3000);
        toast.addEventListener('transitionend', function handler(e) {
            if (e.propertyName === 'opacity' && toast.classList.contains('hide')) {
                toast.removeEventListener('transitionend', handler);
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }
        });
    }
});
