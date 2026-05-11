function toggle() {
    document.getElementById('auth').classList.toggle('active');
}

function setOk(el) {
    el.classList.remove('field-err');
    el.classList.add('field-ok');
    var next = el.nextElementSibling;
    if (next && next.classList.contains('err-msg')) next.remove();
}

function setErr(el, txt) {
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

function check(el, rule) {
    var val = el.value.trim();
    if (rule.required && val === '') { setErr(el, rule.label + ' est obligatoire.'); return false; }
    if (!rule.required && val === '') { setOk(el); return true; }
    if (rule.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { setErr(el, 'Email invalide.'); return false; }
    if (rule.minLen && val.length < rule.minLen) { setErr(el, rule.label + ' min ' + rule.minLen + ' caracteres.'); return false; }
    if (rule.maxLen && val.length > rule.maxLen) { setErr(el, rule.label + ' max ' + rule.maxLen + ' caracteres.'); return false; }
    if (rule.type === 'number') {
        var n = parseFloat(val);
        if (isNaN(n)) { setErr(el, rule.label + ' doit etre un nombre.'); return false; }
        if (rule.min !== undefined && n < rule.min) { setErr(el, rule.label + ' >= ' + rule.min + '.'); return false; }
        if (rule.max !== undefined && n > rule.max) { setErr(el, rule.label + ' <= ' + rule.max + '.'); return false; }
    }
    if (rule.type === 'select' && val === '') { setErr(el, 'Selectionnez ' + rule.label + '.'); return false; }
    setOk(el);
    return true;
}

function validateForm(formId, rules) {
    var form = document.getElementById(formId);
    var valid = true;
    Object.keys(rules).forEach(function(name) {
        var el = form.elements[name];
        if (el && !check(el, rules[name])) valid = false;
    });
    return valid;
}

function attachLive(formId, rules) {
    var form = document.getElementById(formId);
    if (!form) return;
    Object.keys(rules).forEach(function(name) {
        var el = form.elements[name];
        if (!el) return;
        el.addEventListener('blur', function() { check(el, rules[name]); });
        el.addEventListener('input', function() {
            if (el.classList.contains('field-err') || el.classList.contains('field-ok')) {
                check(el, rules[name]);
            }
        });
        if (el.tagName === 'SELECT') {
            el.addEventListener('change', function() { check(el, rules[name]); });
        }
    });
}

var loginRules = {
    email:    { required: true,  type: 'email',  label: 'Email' },
    password: { required: true,  type: 'text',   label: 'Mot de passe', minLen: 6, maxLen: 50 }
};

var registerRules = {
    nom:      { required: true,  type: 'text',   label: 'Nom',          minLen: 2,  maxLen: 50 },
    email:    { required: true,  type: 'email',  label: 'Email' },
    password: { required: true,  type: 'text',   label: 'Mot de passe', minLen: 6,  maxLen: 50 },
    age:      { required: false, type: 'number', label: 'Age',          min: 1,     max: 120 },
    poids:    { required: false, type: 'number', label: 'Poids',        min: 1,     max: 500 },
    taille:   { required: false, type: 'number', label: 'Taille',       min: 30,    max: 300 },
    maladie:  { required: false, type: 'text',   label: 'Maladie',      minLen: 0,  maxLen: 100 },
    role:     { required: true,  type: 'select', label: 'le role' },
    objectif: { required: true,  type: 'select', label: 'un objectif' },
    activite: { required: true,  type: 'select', label: "le niveau activite" }
};

var lf = document.getElementById('loginForm');
var rf = document.getElementById('registerForm');

if (lf) {
    lf.setAttribute('novalidate', 'novalidate');
    lf.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (validateForm('loginForm', loginRules)) lf.submit();
    });
    attachLive('loginForm', loginRules);
}

if (rf) {
    rf.setAttribute('novalidate', 'novalidate');
    rf.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (validateForm('registerForm', registerRules)) rf.submit();
    });
    attachLive('registerForm', registerRules);
}
