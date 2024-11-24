const stripe = Stripe(stripePublicKey);


const elements = stripe.elements({ clientSecret });
const paymentElement = elements.create("payment");
paymentElement.mount("#payment-element");
checkStatus();


document.querySelector("#payment-form").addEventListener("submit", handleSubmit);


async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const { error } = await stripe.confirmPayment({

        elements,
        confirmParams: { // Make sure to change this to your payment completion page
            return_url: redirectAfterSuccessURL

        }
    });


    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        console.log("An unexpected error occured.");
    }
    setLoading(false);
}


async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get("payment_intent_client_secret");
    if (!clientSecret) {
        return;
    }


    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {
        case "succeeded":
            showMessage("Payment succeeded!");
            break;
        case "processing":
            showMessage("Your payment is processing.");
            break;
        case "requires_payment_method":
            showMessage("Your payment was not successful, please try again.");
            break;
        default:
            showMessage("Something went wrong.");
            break;
    }
}

function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function() {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
    }, 4000);
}

function setLoading(isLoading) {
    if (isLoading) { // Disable the button and show a spinner
        document.querySelector("#submit").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
    } else {
        document.querySelector("#submit").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
    }
}