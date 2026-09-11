function showPasswords() {
    document.querySelectorAll(".password-input-field").forEach(function(input) {
        input.type = input.type === "text" ? "password" : "text";
    })
}