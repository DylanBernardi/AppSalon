let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); // Muestra y oculta las secciones
  tabs(); // Cambia la seccion cuando se presionen los botones
  botonesPaginador();
  paginaSiguiente();
  paginaAnterior();
  consultarAPI(); // Consulta la API en el backend de php
  idCliente();
  nombreCliente(); // Añade nombre de cliente al objeto de cita
  seleccionarFecha(); // Añade la fecha de la cita en el objeto
  seleccionarHora(); // Añade la hora de la cita en el objeto
}

function mostrarSeccion() {
  //Ocultar la seccion que tenga la clase de mostrar
  const seccionAnterior = document.querySelector(".mostrar");
  if (seccionAnterior) {
    seccionAnterior.classList.remove("mostrar");
  }

  // Seleccionar la seccion con el paso
  const pasoSelector = `#paso-${paso}`;
  const seccion = document.querySelector(pasoSelector);
  seccion.classList.add("mostrar");

  //Quita la clase actual al tab anterior
  const tabAnterior = document.querySelector(".actual");
  if (tabAnterior) {
    tabAnterior.classList.remove("actual");
  }

  //Resaltar tab acual
  const tab = document.querySelector(`[data-paso="${paso}"]`);
  tab.classList.add("actual");
}

function tabs() {
  //Agrega y cambia la variable segun paso seleccionado
  const botones = document.querySelectorAll(".tabs button");
  botones.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      paso = parseInt(e.target.dataset.paso);
      mostrarSeccion();
      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const paginaSiguiente = document.querySelector("#siguiente");
  const paginaAnterior = document.querySelector("#anterior");

  if (paso == 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso == 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");
    mostrarResumen(); // Muestra el resumen de la cita
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }
  mostrarSeccion();
}

function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", function () {
    if (paso <= pasoInicial) return;
    paso--;
    botonesPaginador();
  });
}

function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", function () {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    const url = "/api/servicios";
    const resultado = await fetch(url);
    const servicios = await resultado.json();
    mostrarServicios(servicios);
  } catch (error) {
    console.log(error);
  }
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    const nombreServicio = document.createElement("P");
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    const servicioDiv = document.createElement("DIV");
    servicioDiv.classList.add("servicio");
    servicioDiv.dataset.idServicio = id;
    servicioDiv.onclick = function () {
      seleccionarServicio(servicio);
    };

    servicioDiv.appendChild(nombreServicio);
    servicioDiv.appendChild(precioServicio);

    document.querySelector("#servicios").appendChild(servicioDiv);
  });
}

function seleccionarServicio(servicio) {
  const { servicios } = cita;
  const { id } = servicio;

  //Identificar el elemento al que se le da click
  const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

  //Comprobar si un servicio ya fue agregado
  if (servicios.some((agregado) => agregado.id === id)) {
    cita.servicios = servicios.filter((agregado) => agregado.id != id);
    divServicio.classList.remove("seleccionado");
  } else {
    cita.servicios = [...servicios, servicio];
    divServicio.classList.add("seleccionado");
  }
}

function nombreCliente() {
  const nombre = document.querySelector("#nombre").value;
  cita.nombre = nombre;
}

function seleccionarFecha() {
  const inputFecha = document.querySelector("#fecha");
  inputFecha.addEventListener("input", function (e) {
    const dia = new Date(e.target.value).getUTCDay();
    if ([6, 0].includes(dia)) {
      e.target.value = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      cita.fecha = e.target.value;
    }
  });
}

function seleccionarHora() {
  const inputHora = document.querySelector("#hora");
  inputHora.addEventListener("input", function (e) {
    const hora = e.target.value.split(":")[0];
    if (hora < 10 || hora > 18) {
      e.target.value = "";
      mostrarAlerta(
        "El salon esta abierto de 10hs a 18hs",
        "error",
        ".formulario"
      );
    } else {
      cita.hora = e.target.value;
    }
  });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
  //previene que se generen mas de 1 alerta
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  //script para generar alertas
  const alerta = document.createElement("DIV");
  alerta.textContent = mensaje;
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);
  const referencia = document.querySelector(elemento);
  referencia.appendChild(alerta);

  //Eliminar alerta
  if (desaparece) {
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");
  //Limpiar contenido resumen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }

  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    mostrarAlerta(
      "faltan datos de Servicios, fecha u hora",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }
  // Formatear el div de resumen
  const { nombre, fecha, hora, servicios } = cita;

  //Heading para servicios en resumen
  const headingServicios = document.createElement("H3");
  headingServicios.textContent = "Resumen servicios";
  resumen.appendChild(headingServicios);

  //Iterar y mostrar servicios
  servicios.forEach((servicio) => {
    const { precio, nombre } = servicio;

    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);

    resumen.appendChild(contenedorServicio);
  });

  const headingCita = document.createElement("H3");
  headingCita.textContent = "Resumen Cita";
  resumen.appendChild(headingCita);

  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

  //Formatear la fecha en español
  const fechaObj = new Date(fecha);
  const mes = fechaObj.getMonth();
  const dia = fechaObj.getDate() + 2;
  const year = fechaObj.getFullYear();

  const fechaUTC = new Date(Date.UTC(year, mes, dia));
  const opciones = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };

  const fechaFormateada = fechaUTC.toLocaleDateString("es-AR", opciones);

  const fechaCita = document.createElement("P");
  fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

  //Boton crear cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("boton");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);
  resumen.appendChild(botonReservar);
}

function idCliente() {
  const idCliente = document.querySelector("#id").value;
  cita.id = idCliente;
}

async function reservarCita() {
  const { id, fecha, hora, servicios } = cita;
  const idServicios = servicios.map((servicio) => servicio.id);
  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("usuarioId", id);
  datos.append("servicios", idServicios);

  //Peticion hacia la API
  try {
    const url = "/api/citas";
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos,
    });
    const resultado = await respuesta.json();
    if (resultado.resultado) {
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        button: "OK",
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un error al crear la cita",
    });
  }
}
