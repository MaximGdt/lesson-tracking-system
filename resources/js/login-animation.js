document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="/login"]');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Добавим анимацию входа
            loginForm.classList.add('login-animation');
            
            setTimeout(() => {
                // Отправим форму после анимации
                e.target.submit();
            }, 1000);
        });
    }
});