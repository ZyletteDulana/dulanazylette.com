<script>
    if (!window.__stockCheckerRunning) {
        window.__stockCheckerRunning = true;

        // Warning sound
        const alertSound = new Audio("/assets/custom/audio/warning-sound.mp3");
        alertSound.preload = "auto";

        let soundUnlocked = localStorage.getItem("audioUnlocked") === "true";


        // ==========================================
        // UNLOCK AUDIO AFTER USER INTERACTION
        // ==========================================
        function unlockAudio() {

            if (soundUnlocked) {
                return;
            }

            alertSound.play()
                .then(() => {

                    alertSound.pause();
                    alertSound.currentTime = 0;

                    soundUnlocked = true;

                    localStorage.setItem("audioUnlocked", "true");

                    console.log("Audio unlocked successfully.");

                })
                .catch(error => {

                    console.log("Audio unlock failed:", error);

                });
        }


        // User interaction
        document.addEventListener("click", unlockAudio, { once: true });
        document.addEventListener("keydown", unlockAudio, { once: true });


        // ==========================================
        // SHOW LOW STOCK TOAST
        // ==========================================
        function showLowStockToast(message) {

            const toastEl = document.getElementById("lowStockToast");
            const toastBody = document.getElementById("toastBody");
            const alertDate = document.getElementById("alertDate");

            if (!toastEl || !toastBody || !alertDate) {
                console.error("Low stock toast elements not found.");
                return;
            }


            toastBody.innerHTML = message;


            alertDate.innerHTML = new Date().toLocaleString(
                "en-PH",
                {
                    timeZone: "Asia/Manila",
                    hour: "numeric",
                    minute: "2-digit",
                    hour12: true
                }
            );


            const toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });

            toast.show();


            // ==========================================
            // PLAY WARNING SOUND
            // ==========================================
            if (soundUnlocked) {

                alertSound.currentTime = 0;

                alertSound.play()
                    .then(() => {

                        console.log("Low stock warning sound played.");

                    })
                    .catch(error => {

                        console.error("Sound could not play:", error);

                    });

            } else {

                console.log("Sound is not unlocked yet.");

            }


            const toastWrapper = document.getElementById("toastWrapper");

            if (toastWrapper) {
                toastWrapper.style.display = "block";
            }
        }


        // ==========================================
        // CHECK LOW STOCK
        // ==========================================
        function checkLowStock() {

            fetch("/api/stockChecker.php", {
                method: "GET",
                cache: "no-cache"
            })

            .then(response => {

                if (!response.ok) {
                    throw new Error(
                        "Stock checker request failed: " + response.status
                    );
                }

                return response.json();

            })

            .then(data => {

                console.log("Stock checker response:", data);


                if (data.success && Number(data.data) > 0) {

                    showLowStockToast(
                        `⚠️ You have <strong>${data.data}</strong> item(s) on low stock!
                        <br>
                        <a href="dashboard.php?page=inventory-list">
                            Click here to view inventory
                        </a>`
                    );

                }

            })

            .catch(error => {

                console.error("Stock checker error:", error);

            });
        }


        // ==========================================
        // INITIAL CHECK
        // ==========================================
        checkLowStock();


        // ==========================================
        // CHECK EVERY 10 MINUTES
        // ==========================================
        setInterval(checkLowStock, 600000);

    }
</script>