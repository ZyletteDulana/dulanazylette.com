const input = document.getElementById('imageInput');
const preview = document.getElementById('imagePreview');

input.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
        if (allowedTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }

        else {
            this.value = '';
            preview.src = 'assets/global/image/gmb-logo.small.png';
        }
    }
});