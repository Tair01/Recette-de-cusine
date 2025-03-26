const select = document.querySelector('.change-lang');
const allLang = ['en', 'fr'];
function loadLanguage() {
    let hash = window.location.hash.substr(1);
    let savedLang = localStorage.getItem('lang');

    if (allLang.includes(hash)) {
        localStorage.setItem('lang', hash); // On sauvegarde la langue depuis l'URL
    } else if (savedLang && allLang.includes(savedLang)) {
        window.location.hash = savedLang; // On installe la langue enregistrée
    } else {
        window.location.hash = 'fr'; // langue par defaut
        localStorage.setItem('lang', 'fr');
    }

    changeLanguage(); // On applique la traduction
}
// Redirection vers l'URL avec la langue qu'on a spécifiée
function changeURLLanguage() {
    let lang = select.value;
    localStorage.setItem('lang', lang); // On sauvegarde le choix
    history.replaceState(null, null, '#' + lang); // On change le hash sans un reload de la page
    changeLanguage();
}

// Fonction de changement de langue avec contrôle de la présence d'éléments
function changeLanguage() {
    let hash = window.location.hash.substr(1);
    if (!allLang.includes(hash)) {
        location.href = window.location.pathname + '#fr';
        location.reload();
    }
    if (select) {
        select.value = hash;
    }
    // Éléments communs à toutes les pages
    if (document.querySelector('title')) {
        document.querySelector('title').innerHTML = langArr['unit'][hash];
    }
    document.querySelectorAll('[data-lang-' + hash + ']').forEach(function (elem) {
        let translation = elem.getAttribute('data-lang-' + hash);
        if (translation) {
            elem.textContent = translation;
        }
    });
    if (document.querySelector('.header__title')) {
        let headerTitle = document.querySelector('.header__title');
        let lang = hash; // La langue courrant depuisl l'URL
        headerTitle.innerHTML = headerTitle.getAttribute(`data-lang-${lang}`);
    }
    if (document.querySelector('.header-text')) {
        document.querySelector('.header-text').innerHTML = langArr['header-text'][hash];
    }

    if (document.getElementById('title-1')) {
        document.getElementById('title-1').innerHTML = langArr['title-1'][hash];
    }
    // On verifie la présence des éléments avant de les changer
    let ids = ['name1', 'name2', 'name3', 'name4', 'name5', 'name6'];
    ids.forEach(id => {
        let elem = document.getElementById(id);
        if (elem) {
            elem.innerHTML = langArr[id][hash];
        }
    });

    let addRecette = document.getElementById('cusine-add');
    if (addRecette) {
        addRecette.innerHTML = langArr['cusine-add'][hash];
    }

    // Page de Profile
    if (window.location.pathname.includes("profile.php")) {
        let profileT = document.getElementById('title-1-profile');
        if (profileT) {
            profileT.innerHTML = langArr['title-1-profile'][hash];
        }
        let profileP = document.getElementById('profileP');
        if (profileP) {
            profileP.innerHTML = langArr['profileP'][hash];
        }
        let profileE = document.getElementById('profileE');
        if (profileE) {
            profileE.innerHTML = langArr['profileE'][hash];
        }
        let profileR = document.getElementById('profileR');
        if (profileR) {
            profileR.innerHTML = langArr['profileR'][hash];
        }
        let profileR2 = document.getElementById('profileR2');
        if (profileR2) {
            profileR2.innerHTML = langArr['profileR2'][hash];
        }
        let changeR = document.getElementById('changeRP');
        if (changeR) {
            changeR.innerHTML = langArr['changeRP'][hash];
        }
        let demandeR = document.getElementById('request-role-btn');
        if (demandeR) {
            demandeR.innerHTML = langArr['request-role-btn'][hash];
        }
        let newPass = document.getElementById('new-pass');
        if (newPass) {
            newPass.innerHTML = langArr['new-pass'][hash];
        }
        let newPassBtn = document.getElementById('newPassBtn');
        if (newPassBtn) {
            newPassBtn.innerHTML = langArr['newPassBtn'][hash];
        }
        let deconnexionP = document.getElementById('logout-btn');
        if (deconnexionP) {
            deconnexionP.innerHTML = langArr['logout-btn'][hash];
        }
    }
    // Page d'administrateur 
    if (window.location.pathname.includes("admin.php")) {
        let adminPanel = document.getElementById('title-1-admin');
        if (adminPanel) {
            adminPanel.innerHTML = langArr['title-1-admin'][hash];
        }
        let adminPrenom = document.getElementById('prenom-admin');
        if (adminPrenom) {
            adminPrenom.innerHTML = langArr['prenom-admin'][hash];
        }
        let adminEmail = document.getElementById('email-admin');
        if (adminEmail) {
            adminEmail.innerHTML = langArr['email-admin'][hash];
        }
        let adminRole = document.getElementById('role-admin');
        if (adminRole) {
            adminRole.innerHTML = langArr['role-admin'][hash];
        }
    }
    // La page de modification d'une recette choisi
    if (window.location.pathname.includes("modifier_recette.php")) {
        let modifTitleRct = document.getElementById('modifTitleRct');
        if (modifTitleRct) {
            modifTitleRct.innerHTML = langArr['modifTitleRct'][hash];
        }
        let ajoutRctTitreFr = document.getElementById('ajoutRctTitreFr');
        if (ajoutRctTitreFr) {
            ajoutRctTitreFr.innerHTML = langArr['ajoutRctTitreFr'][hash];
        }
        let ajoutRctTitreEn = document.getElementById('ajoutRctTitreEn');
        if (ajoutRctTitreEn) {
            ajoutRctTitreEn.innerHTML = langArr['ajoutRctTitreEn'][hash];
        }
        let ajoutRctIngrFr = document.getElementById('ajoutRctIngrFr');
        if (ajoutRctIngrFr) {
            ajoutRctIngrFr.innerHTML = langArr['ajoutRctIngrFr'][hash];
        }
        let ajoutRctIngrEn = document.getElementById('ajoutRctIngrEn');
        if (ajoutRctIngrEn) {
            ajoutRctIngrEn.innerHTML = langArr['ajoutRctIngrEn'][hash];
        }
        let ajoutRctEtapFr = document.getElementById('ajoutRctEtapFr');
        if (ajoutRctEtapFr) {
            ajoutRctEtapFr.innerHTML = langArr['ajoutRctEtapFr'][hash];
        }
        let ajoutRctEtapEn = document.getElementById('ajoutRctEtapEn');
        if (ajoutRctEtapEn) {
            ajoutRctEtapEn.innerHTML = langArr['ajoutRctEtapEn'][hash];
        }
        let ajoutRctGlut = document.getElementById('ajoutRctGlut');
        if (ajoutRctGlut) {
            ajoutRctGlut.innerHTML = langArr['ajoutRctGlut'][hash];
        }
        let ajoutRctStatus = document.getElementById('ajoutRctStatus');
        if (ajoutRctStatus) {
            ajoutRctStatus.innerHTML = langArr['ajoutRctStatus'][hash];
        }
        let modifBtnRct = document.getElementById('modifierRct');
        if (modifBtnRct) {
            modifBtnRct.innerHTML = langArr['modifierRct'][hash];
        }
        let deleteBtnRct = document.getElementById('delete-btn-rct');
        if (deleteBtnRct) {
            deleteBtnRct.innerHTML = langArr['delete-btn-rct'][hash];
        }
    }
    // La page de login
    if (window.location.pathname.includes("login.html")) {
        let loginSlt = document.getElementById('loginSlt');
        if (loginSlt) {
            loginSlt.innerHTML = langArr['loginSlt'][hash];
        }
        let motDePasseOubl = document.getElementById('motDePasseOubl');
        if (motDePasseOubl) {
            motDePasseOubl.innerHTML = langArr['motDePasseOubl'][hash];
        }
        let connectBtnLogin = document.getElementById('connectBtnLogin');
        if (connectBtnLogin) {
            connectBtnLogin.innerHTML = langArr['connectBtnLogin'][hash];
        }
        let ouLogin = document.getElementById('ouLogin');
        if (ouLogin) {
            ouLogin.innerHTML = langArr['ouLogin'][hash];
        }
        let showRegisterForm = document.getElementById('showRegisterForm');
        if (showRegisterForm) {
            showRegisterForm.innerHTML = langArr['showRegisterForm'][hash];
        }
        let enregistreLogin = document.getElementById('enregistreLogin');
        if (enregistreLogin) {
            enregistreLogin.innerHTML = langArr['enregistreLogin'][hash];
        }
        let ouLogin2 = document.getElementById('ouLogin2');
        if (ouLogin2) {
            ouLogin2.innerHTML = langArr['ouLogin2'][hash];
        }
        let showLoginForm = document.getElementById('showLoginForm');
        if (showLoginForm) {
            showLoginForm.innerHTML = langArr['showLoginForm'][hash];
        }
        let verifyBtn = document.getElementById('verifyBtn');
        if (verifyBtn) {
            verifyBtn.innerHTML = langArr['verifyBtn'][hash];
        }
    }
    // La page Reset password ou on recoit un nouveau mot de pass
    if (window.location.pathname.includes("reset_pass.html")) {
        let resetPass = document.getElementById('resetPass');
        if (resetPass) {
            resetPass.innerHTML = langArr['resetPass'][hash];
        }
        let conseilPass = document.getElementById('conseilPass');
        if (conseilPass) {
            conseilPass.innerHTML = langArr['conseilPass'][hash];
        }
        let resetEmailBtn = document.getElementById('resetEmailBtn');
        if (resetEmailBtn) {
            resetEmailBtn.innerHTML = langArr['resetEmailBtn'][hash];
        }
    }
}
window.addEventListener('hashchange', changeLanguage);
loadLanguage();
if (select) {
    select.addEventListener('change', changeURLLanguage);
}