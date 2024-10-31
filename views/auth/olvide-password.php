<h1 class="nombre-pagina">Olvide Contraseña</h1>
<p class="descripcion-pagina">Restablece tu contraseña escribiendo tu email a continuacion</p>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form method="POST" action="/olvide" class="formulario">

    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email"
            id="email"
            name="email"
            placeholder="Tu E-mail">
    </div>

    <input type="submit" class="boton" value="Enviar Intrucciones">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesion</a>
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear Una</a>
</div>