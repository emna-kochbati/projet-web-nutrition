function setOkP(el) {
    el.classList.remove('field-err');
    el.classList.add('field-ok');
    var next = el.nextElementSibling;
    if (next && next.classList.contains('err-msg')) next.remove();
}

function setErrP(el, txt) {
    el.classList.remove('field-ok');
    el.classList.add('field-err');
    var msg = el.nextElementSibling;
    if (!msg || !msg.classList.contains('err-msg')) {
        msg = document.createElement('span');
        msg.classList.add('err-msg');
        el.insertAdjacentElement('afterend', msg);
    }
    msg.textContent = txt;
}

function clearP(el) {
    el.classList.remove('field-ok', 'field-err');
    var next = el.nextElementSibling;
    if (next && next.classList.contains('err-msg')) next.remove();
}

function validateProfile() {
    var form = document.getElementById('profileForm');
    if (!form) return true;

    var valid = true;

    var nom    = form.elements['nom'];
    var email  = form.elements['email'];
    var age    = form.elements['age'];
    var poids  = form.elements['poids'];
    var taille = form.elements['taille'];

    /* NOM */
    if (nom) {
        var nomVal = nom.value.trim();
        if (nomVal.length < 2 || nomVal.length > 50) {
            setErrP(nom, 'Nom : 2 a 50 caracteres.'); valid = false;
        } else { setOkP(nom); }
    }

    /* EMAIL */
    if (email) {
        var emailVal = email.value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            setErrP(email, 'Email invalide.'); valid = false;
        } else { setOkP(email); }
    }

    /* AGE optionnel */
    if (age) {
        var ageVal = age.value.trim();
        if (ageVal === '') {
            clearP(age);
        } else {
            var ageN = parseFloat(ageVal);
            if (isNaN(ageN) || ageN < 1 || ageN > 120) {
                setErrP(age, 'Age : entre 1 et 120.'); valid = false;
            } else { setOkP(age); }
        }
    }

    /* POIDS optionnel */
    if (poids) {
        var poidsVal = poids.value.trim();
        if (poidsVal === '') {
            clearP(poids);
        } else {
            var poidsN = parseFloat(poidsVal);
            if (isNaN(poidsN) || poidsN < 1 || poidsN > 500) {
                setErrP(poids, 'Poids : entre 1 et 500.'); valid = false;
            } else { setOkP(poids); }
        }
    }

    /* TAILLE optionnel */
    if (taille) {
        var tailleVal = taille.value.trim();
        if (tailleVal === '') {
            clearP(taille);
        } else {
            var tailleN = parseFloat(tailleVal);
            if (isNaN(tailleN) || tailleN < 30 || tailleN > 300) {
                setErrP(taille, 'Taille : entre 30 et 300.'); valid = false;
            } else { setOkP(taille); }
        }
    }

    return valid;
}

var profileForm = document.getElementById('profileForm');

if (profileForm) {
    profileForm.setAttribute('novalidate', 'novalidate');

    /* Submit */
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (validateProfile()) profileForm.submit();
    });

    /* Live validation */
    var inputs = profileForm.querySelectorAll('input, select');
    inputs.forEach(function(el) {
        el.addEventListener('blur', validateProfile);
        el.addEventListener('input', function() {
            if (el.classList.contains('field-err') || el.classList.contains('field-ok')) {
                validateProfile();
            }
        });
    });
}
