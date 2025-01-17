document
    .getElementById("resendForm")
    .addEventListener("submit", function (event) {
        event.preventDefault();
        const form = event.target;

        fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                    .value,
            },
        })
            .then((response) => {
                if (response.ok) {
                    document.getElementById("alertMessage").style.display =
                        "block";
                    document.getElementById("resendForm").style.display =
                        "none";
                } else {
                    throw new Error("Failed to resend verification email.");
                }
            })
            .catch((error) => {
                alert(error.message);
            });
    });
