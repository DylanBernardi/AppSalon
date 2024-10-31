<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesion con tus datos</p>

<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<form class="formulario" action="/" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu Email" name="email" value="<?= s($auth->email) ?>">
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Tu Contraseña" name="password">
    </div>

    <input type="submit" value="Iniciar Sesion" class="boton">

</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una</a>
    <a href="/olvide">¿Olvidaste tu contraseña?</a>
</div>