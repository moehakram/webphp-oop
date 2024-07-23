document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        const small = input.nextElementSibling;
        if (small && small.textContent.trim() !== '') {
            input.classList.add('error');
        }
    });
});
