// Находим модальное окно и кнопку "Зарегистрироваться"
var modal = document.getElementById('register-modal');
var registerButton = modal.querySelector('input[type="submit"]');

// Добавляем обработчик события на кнопку "Зарегистрироваться"
registerButton.addEventListener('click', function (event) {
    event.preventDefault(); // Предотвращаем стандартное поведение кнопки

    // Получаем данные из формы
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    // Создаем объект FormData для отправки данных формы через AJAX
    var formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);

    // Отправляем AJAX-запрос на сервер
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/submit-registration', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Обработка успешного ответа от сервера
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Отображаем сообщение об успешной регистрации или выполняем другие действия
                alert('Регистрация успешна!');
            } else {
                // Отображаем сообщение об ошибке регистрации или выполняем другие действия
                alert('Ошибка регистрации: ' + response.error);
            }
        } else {
            // Обработка ошибки AJAX-запроса
            alert('Произошла ошибка при отправке запроса на сервер.');
        }
    };
    xhr.onerror = function () {
        // Обработка ошибки AJAX-запроса
        alert('Произошла ошибка при отправке запроса на сервер.');
    };
    xhr.send(formData);
});


// Функция для отправки формы входа на сервер
function submitLoginForm() {
    // Получаем данные из формы
    var username = document.getElementById('login-username').value;
    var password = document.getElementById('login-password').value;

    // Отправляем AJAX запрос на сервер
    $.ajax({
        url: '/submit-login',
        method: 'POST',
        data: {
            username: username,
            password: password
        },
        success: function (response) {
            // Если вход успешен, перезагружаем страницу
            if (response.success) {
                window.location.reload();
            } else {
                // Если произошла ошибка, показываем сообщение об ошибке
                alert('Ошибка входа: ' + response.error);
            }
        },
        error: function (xhr, status, error) {
            // В случае ошибки AJAX запроса, показываем сообщение об ошибке
            alert('Ошибка: ' + error);
        }
    });
}

// Обработчик события отправки формы
document.getElementById('login-form').addEventListener('submit', function (event) {
    event.preventDefault();
    submitLoginForm();
});

var token = document.getElementById('ulogin-token').value;
var formData = new FormData();
formData.append('token', token);

var xhr = new XMLHttpRequest();
xhr.open('POST', '/ulogin', true);
xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 300) {
        window.location.href = '/';
    } else {
        console.error('Ошибка при отправке запроса:', xhr.responseText);
    }
};

xhr.onerror = function () {
    console.error('Ошибка сети');
};

xhr.send(formData);
