async function sendForm() {
  const formulario = document.getElementById("formcriterios");
  const formdata = new FormData(formulario);

  try {
    const response = await fetch(
      "http://localhost/criterios/backend/registerMatriz.php",
      {
        method: "POST",
        body: formdata,
      }
    );

    const data = await response.json();
    console.log(data);

    clearForm();
  } catch (error) {
    console.log("Error al enviar formulario:", error);
  }
}

function clearForm() {
  document.getElementById("formcriterios").reset();
}
