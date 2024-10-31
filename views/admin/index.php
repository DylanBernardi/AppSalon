<h1 class="nombre-pagina">Panel de administracion</h1>
<?php
include_once __DIR__ . '/../templates/barra.php';
?>
<h2>Buscar citas</h2>
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" />
        </div>
    </form>
</div>
<div class="citas-admin">
    <ul class="citas">
        <?php
        $idCita = 0;
        $totalCitas = count($citas);

        foreach ($citas as $index => $cita) {
            if ($idCita !== $cita->id) {
                $totPrecio = 0;
                $idCita = $cita->id;
        ?>
                <li>
                    <p>ID: <span><?= $cita->id ?></span></p>
                    <p>Hora: <span><?= $cita->hora ?></span></p>
                    <p>Cliente: <span><?= $cita->cliente ?></span></p>
                    <p>Email: <span><?= $cita->email ?></span></p>
                    <p>Telefono: <span><?= $cita->telefono ?></span></p>
                    <h3>Servicios</h3>

                <?php
            } //FIN de IF
                ?>
                <p class="servicio"><?= $cita->servicio . " " . $cita->precio ?></p>
                <?php
                $totPrecio += $cita->precio;
                if ($index === $totalCitas - 1 || $citas[$index + 1]->id !== $cita->id) { ?>
                    <p>Total a pagar <?php echo $totPrecio; ?></p>

                    <form action="/api/eliminar" method="POST">
                        <input type="hidden" name="id" value="<?= $cita->id ?>">
                        <input type="submit" class="boton-eliminar" value="Eliminar">
                    </form>
            <?php
                }
            } //FIN de FOREACH 
            ?>
    </ul>

</div>

<?php
$script = "<script src='build/js/buscador.js'></script>";
?>

<?php if (empty($citas)) : ?>
    <h2 class="Mycita">No hay citas reservadas este dia</h2>
<?php
endif ?>