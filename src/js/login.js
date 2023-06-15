docReady(() => {

    'use strict';
    const d = document;
    const loginBtn = d.querySelector('#loginBtn');
    const login = d.querySelector('#login');
    const password = d.querySelector('#password');
    const form = d.querySelector('.form-signin');
    let csrfToken;
    if (d.querySelector('meta[name="csrf-token"]') !== null) {
        csrfToken = d.querySelector('meta[name="csrf-token"]').content;
    }
    const ajaxHeaders = {
        'credentials': 'same-origin',
        'X-Requested-With': 'XMLHttpRequest',
        'cache': 'no-cache',
        'Cache-Control': 'no-store, no-transform, max-age=0, private',
        'Content-Type': 'application/json'
    };
    if (csrfToken !== null) {
        ajaxHeaders['X-CSRF-TOKEN'] = csrfToken;
    }

    const markLoginError = () => {
        form.classList.add('error');
        setTimeout(() => {
            form.classList.remove('error');
        }, 1000);
    };

    if (loginBtn !== null) {
        loginBtn.addEventListener('click', () => {
            if (login.value.length === 0 || password.value.length === 0) {
                markLoginError();
            } else {

                fetch('cnx.php', {
                    headers: ajaxHeaders,
                    method: 'post',
                    redirect: 'follow',
                    body: JSON.stringify({
                        "type": "cnx",
                        "action": "connect",
                        "login": window.btoa(AesJson.encrypt(login.value, csrfToken)),
                        "pwash": window.btoa(AesJson.encrypt(password.value, csrfToken))
                    })
                }).then((response) => {
                    return response.json();
                }).then((cnx) => {
                    if (cnx.status === 401 && cnx.action === 'cnx' && cnx.connected === false) {
                        markLoginError();
                    } else if (cnx.status === 200 && cnx.action === 'cnx' && cnx.connected === true) {
                        d.location.href = 'dashboard.php';
                    }
                });
            }
        });
    }
});
