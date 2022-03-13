let _form = document.getElementById('nttek-form-login'),
  _loginButton = document.getElementById('nttek-login');

function createAlert(_message = "None", _type = "alert-warning", _always = false) {
  var mainDiv = document.createElement("div"),
      buttonDismiss = document.createElement("button"),
      spanMessage = document.createElement("span"),
      id = `alert-${Date.now()}`;

  mainDiv.setAttribute("class", `alert ${_type} alert-dismissible fade show`);
  mainDiv.setAttribute("role", "alert");
  mainDiv.setAttribute("id", id);
  buttonDismiss.setAttribute("type", "button");
  buttonDismiss.setAttribute("class", "btn-close");
  buttonDismiss.setAttribute("data-bs-dismiss", "alert");
  buttonDismiss.setAttribute("aria-label", "Close");
  spanMessage.innerHTML = _message;
  mainDiv.appendChild(buttonDismiss);
  mainDiv.appendChild(spanMessage);

  document.getElementById("nttek-alerts-area").appendChild(mainDiv);
  if (!_always)
      setTimeout(() => {
          if (document.getElementById(id) !== null)
              document.getElementById(id).remove();
      }, 3000);
}

function findGetParameter(parameterName) {
  var result = null,
      tmp = [];
  location.search
      .substr(1)
      .split("&")
      .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
      });
  return result;
}

_loginButton.onclick = async () => {
  if (_form.checkValidity()) {
    _form.classList.remove('was-validated');

      let _formData = new FormData(_form);
      let response = await fetch('/service/user/login', {
        method: 'POST',
        body: _formData
      });
      if (response.ok) {
        let answer = await response.json();
        location.href = `${findGetParameter('redirect')}?jwt=${answer.payload.jwt}&refresh=${answer.payload.refresh}`;
      } else {
        switch (response.status) {
          case 405:
          case 400:
            createAlert(
              '<b>Ошибка!</b> Произошла системная ошибка. Обратитесь к администратору.',
              'alert-danger'
            );
          break;
          case 401:
            createAlert(
              '<b>Внимание!</b> Скорее всего, вы ввели неправильные данные для входа. Попробуйте ещё раз.'
            );
          break;
        }
    }
  } else {
    _form.classList.add('was-validated');
  }
}