document.addEventListener('DOMContentLoaded', () => {
    const repeatEmails = document.querySelectorAll('input[name="email-2"]');
    
    repeatEmails.forEach(email => {
        email.value = "";
        email.addEventListener('paste', e => e.preventDefault());
    });
});