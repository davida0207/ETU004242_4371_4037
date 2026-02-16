document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#registerForm");
    if (!form) return;

    const statusBox = document.querySelector("#formStatus");

    const map = {
        nom: { input: "#nom", err: "#nomError" },
        prenom: { input: "#prenom", err: "#prenomError" },
        email: { input: "#email", err: "#emailError" },
        password: { input: "#password", err: "#passwordError" },
        confirm_password: { input: "#confirm_password", err: "#confirmPasswordError" },
        telephone: { input: "#telephone", err: "#telephoneError" },
    };

    function setStatus(type, msg) {
        if (!statusBox) return;
        if (!msg) {
            statusBox.className = "alert d-none";
            statusBox.textContent = "";
            return;
        }
        statusBox.className = `alert alert-${type}`;
        statusBox.textContent = msg;
    }

    function clearFeedback() {
        Object.keys(map).forEach((k) => {
            const input = document.querySelector(map[k].input);
            const err = document.querySelector(map[k].err);
            input.classList.remove("is-invalid", "is-valid");
            if (err) err.textContent = "";
        });
        setStatus(null, "");
    }

    function applyServerResult(data) {
        if (data.values && data.values.telephone) {
            document.querySelector("#telephone").value = data.values.telephone;
        }

        Object.keys(map).forEach((k) => {
            const input = document.querySelector(map[k].input);
            const err = document.querySelector(map[k].err);
            const msg = (data.errors && data.errors[k]) ? data.errors[k] : "";

            if (msg) {
                input.classList.add("is-invalid");
                input.classList.remove("is-valid");
                if (err) err.textContent = msg;
            } else {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
                if (err) err.textContent = "";
            }
        });

        if (data.errors && data.errors._global) {
            setStatus("warning", data.errors._global);
        }
    }

    async function callValidate() {
        const fd = new FormData(form);
        const res = await fetch("/api/validate/register", {
            method: "POST",
            body: fd,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error("Erreur serveur lors de la validation.");
        return res.json();
    }

    form.addEventListener("submit", async(e) => {
        e.preventDefault();
        clearFeedback();

        try {
            const data = await callValidate();
            applyServerResult(data);

            if (data.ok) {
                setStatus("success", "Validation OK ✅ Envoi en cours...");
                form.submit();
            } else {
                setStatus("danger", "Veuillez corriger les erreurs.");
            }
        } catch (err) {
            setStatus("warning", err.message || "Une erreur est survenue.");
        }
    });

    Object.keys(map).forEach((k) => {
        document.querySelector(map[k].input).addEventListener("blur", async() => {
            try {
                const data = await callValidate();
                applyServerResult(data);
            } catch (_) {}
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#loginForm");
    if (!form) return;

    const statusBox = document.querySelector("#formStatus");

    const fields = {
        nom: { input: "#nom", err: "#nomError", label: "Nom" },
        email: { input: "#email", err: "#emailError", label: "Email" },
        password: { input: "#password", err: "#passwordError", label: "Mot de passe" },
    };

    function setStatus(type, msg) {
        if (!statusBox) return;
        if (!msg) {
            statusBox.className = "alert d-none";
            statusBox.textContent = "";
            return;
        }
        statusBox.className = `alert alert-${type}`;
        statusBox.textContent = msg;
    }

    function setFieldError(key, msg) {
        const input = document.querySelector(fields[key].input);
        const err = document.querySelector(fields[key].err);
        if (!input) return;
        if (msg) {
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            if (err) err.textContent = msg;
        } else {
            input.classList.remove("is-invalid");
            input.classList.add("is-valid");
            if (err) err.textContent = "";
        }
    }

    function clear() {
        Object.keys(fields).forEach((k) => setFieldError(k, ""));
        setStatus(null, "");
    }

    function validate() {
        const nomEl = document.querySelector("#nom");
        const emailEl = document.querySelector("#email");
        const passwordEl = document.querySelector("#password");

        const nom = ((nomEl && nomEl.value) ? nomEl.value : "").trim();
        const email = ((emailEl && emailEl.value) ? emailEl.value : "").trim();
        const password = (passwordEl && passwordEl.value) ? passwordEl.value : "";

        let ok = true;

        if (nom.length < 2) {
            setFieldError("nom", "Le nom doit contenir au moins 2 caractères.");
            ok = false;
        }

        if (!email) {
            setFieldError("email", "L'email est obligatoire.");
            ok = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setFieldError("email", "L'email n'est pas valide.");
            ok = false;
        }

        if (!password) {
            setFieldError("password", "Le mot de passe est obligatoire.");
            ok = false;
        }

        return ok;
    }

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        clear();

        if (validate()) {
            setStatus("success", "Validation OK ✅");
            form.submit();
        } else {
            setStatus("danger", "Veuillez corriger les erreurs.");
        }
    });

    Object.keys(fields).forEach((k) => {
        const input = document.querySelector(fields[k].input);
        if (!input) return;
        input.addEventListener("blur", () => {
            // Soft re-validate on blur
            clear();
            validate();
        });
    });
});