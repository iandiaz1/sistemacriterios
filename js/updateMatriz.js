function generarNormalizacion() {
  var checkboxes = document.querySelectorAll(
    'input[name="criterios_seleccionados[]"]:checked'
  );
  var selectedIds = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.value;
  });

  var url = "normalizacion.php?criterios=" + selectedIds.join(",");

  window.location.href = url;
}

function mostrarForm(id) {
  document.getElementById("form" + id).style.display = "block";
}

function ocultarForm(id) {
  document.getElementById("form" + id).style.display = "none";
}

function eliminarCriterio(event, criterioId) {
  event.preventDefault();

  if (confirm("¿Estás seguro de que deseas eliminar este criterio?")) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "matriz.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        console.log(xhr.responseText);
        location.reload();
      }
    };

    xhr.send("eliminar=true&id_eliminar=" + criterioId);
  }
}
