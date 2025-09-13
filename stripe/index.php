<form id="payment-form">
  <div id="card-element"></div>
  <button type="submit">Salvar cartão</button>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script>
  const stripe = Stripe("pk_test_51Rv52C6aUFqQ4eZQwdrkSQsguvjYbU9jsHDqXXO42wpRuDrZlRECESd2CdymXD1XzLwhit9HEmKwfgqCAfBd31jh00ZeLWXIoH"); // sua public key
  const elements = stripe.elements();
  const cardElement = elements.create("card");
  cardElement.mount("#card-element");

  const form = document.getElementById("payment-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const clientSecret = "seti_1S6OAO6aUFqQ4eZQ4mwbgbI1_secret_T2T6zBPqhGvl1UY38jc8RlE0JpiMzv3"; // enviado pelo backend
    const { setupIntent, error } = await stripe.confirmCardSetup(
      clientSecret,
      {
        payment_method: {
          card: cardElement,
          billing_details: {
            name: "Cliente Teste",
            email: "teste@example.com"
          }
        }
      }
    );

    if (error) {
      console.error(error);
      alert(error.message);
    } else {
      console.log("PaymentMethod salvo:", setupIntent.payment_method);

      // Enviar payment_method pro backend para criar assinatura
      await fetch("/subscriptions/me", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          customer_id: setupIntent.customer,
          payment_method_id: setupIntent.payment_method,
          price_id: "price_XXXXX"
        })
      });
    }
  });
</script>
