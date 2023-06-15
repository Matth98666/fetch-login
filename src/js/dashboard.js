docReady(() => {

    'use strict';
    const d = document;
    const logoutBtn = d.querySelector('#logout');
    const vid = d.querySelector('#bgvid');
    const email = d.querySelector('#email');
    const login = d.querySelector('#login');
    const password = d.querySelector('#password');
    const pwhash = d.querySelector('#pwhash');
    const moduser = d.querySelector('#moduser');
    const moduserform = d.querySelector('#moduserform');
    const adduser = d.querySelector('#adduser');
    const adduserform = d.querySelector('#adduserform');
    const deleteuser = d.querySelector('#deleteuser');
    const deleteuserform = d.querySelector('#deleteuserform');


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

    /* Logout event */
    if (logoutBtn !== null) {
        logoutBtn.addEventListener('click', () => {
            fetch('logout.php', {
                headers: ajaxHeaders,
                method: 'post',
                body: JSON.stringify({
                    "type": "cnx",
                    "action": "disconnect"
                })
            }).then((response) => {
                return response.json();
            }).then((cnx) => {
                if (cnx.status === 200 && cnx.action === 'cnx' && cnx.disconnected === true) {
                    d.location.href = 'login.php';
                }
            });
        });
    }

    /* Play background video */
    if (vid !== null) {
        vid.play();
    }

    if (moduser !== null) {
        moduser.addEventListener('click', (e) => {
            e.preventDefault();
            if(email.value.trim().length > 0) {
                login.value = window.btoa(AesJson.encrypt(email.value, csrfToken));
            }
            if(password.value.trim().length > 0) {
                pwhash.value = window.btoa(AesJson.encrypt(password.value, csrfToken));
            }
            moduserform.submit();
        });
    }

    if (adduser !== null) {
        adduser.addEventListener('click', (e) => {
            e.preventDefault();
            if(email.value.trim().length > 0) {
                login.value = window.btoa(AesJson.encrypt(email.value, csrfToken));
            }
            if(password.value.trim().length > 0) {
                pwhash.value = window.btoa(AesJson.encrypt(password.value, csrfToken));
            }
            adduserform.submit();
        });
    }

    if (deleteuser !== null) {
        deleteuser.addEventListener('click', (e) => {
            e.preventDefault();
            deleteuserform.submit();
        });
    }
});

