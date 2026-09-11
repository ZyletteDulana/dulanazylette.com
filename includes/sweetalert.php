<!-- SweetAlert CDN (DONE)-->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<!-- SweetAlert CDN -->

<!-- SweetAlert Offline File -->
<!-- <script src="assets/cdn/sweetalert.min.js"></script> -->

<!-- SweetAlert -->
<?php if (isset($_SESSION["notification-status"]) && $_SESSION["notification-status"] !== ""): ?>
    <script>
        const notification = <?php echo $_SESSION["notification-status"]; ?>;
        Swal.fire({
            icon: notification.icon || 'info',
            title: notification.title || '',
            text: notification.message || '',
            showConfirmButton: false,
            timer: 3000
        });
    </script>

    <?php unset($_SESSION["notification-status"]); ?>
<?php endif; ?>
<!-- SweetAlert -->

<!-- Confirm Action 1 -->
<script>
    function confirmAction(event, formElement, formId, title, icon, message, confirmButtonTitle, confirmButtonBgColor) {

        const form = document.getElementById(formId);

        if (!formElement.checkValidity()) {
            formElement.reportValidity();
            return;
        }

        event.preventDefault();

        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonText: confirmButtonTitle,
            cancelButtonText: 'Cancel',
            confirmButtonColor: confirmButtonBgColor,
            cancelButtonColor: '#6c757d',

            didOpen: () => {
            const confirmBtn = document.querySelector('.swal2-confirm');

                if (confirmBtn && confirmButtonBgColor) {
                    confirmBtn.style.cssText = `
                        background:${confirmButtonBgColor};
                        border-color:${confirmButtonBgColor};
                        color:#fff;
                        box-shadow:none;
                        outline:none;
                    `;
                }
            }
            
        }).then((result) => {
            if (result.isConfirmed) {
                formElement.submit();
            }
        });

        return false;
    }
</script>

<!-- Confirm Action 2 -->
<script>
function confirmAction2(event, element, title, icon, message, confirmButtonText, confirmButtonColor) {
    event.preventDefault(); // stop the link from navigating immediately

    const href = element.getAttribute('href'); // get the URL from the <a>

    Swal.fire({
        title: title,
        text: message,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel',
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // redirect to the URL (GET request)
            window.location.href = href;
        }
    });
}
</script>

