<?php
include_once "header.php";
include_once "nav.php";
?>

<body>

<div class="container mt-5">
    <h2 class="mb-4">Integrantes del Proyecto</h2>

    <?php
    // Definir un arreglo con la información de los integrantes
    $integrantes = array(
        array('nombre' => 'Jeancarlos Chafloque Velásquez', 'rol' => 'Desarrollador Hardware, Software , Coordinador'),
        array('nombre' => 'Alonso Luque Villar', 'rol' => 'Desarrollador Hardware'),
        array('nombre' => 'Edinson Patiño Sanchez', 'rol' => 'Desarrollador Software, Testing,Documentación'),
        array('nombre' => 'Luz Maria Quevedo Diaz', 'rol' => 'Desarrollador Software, Testing,Documentación')
        // Agrega más integrantes según sea necesario
    );

    // Iterar a través de los integrantes y mostrar la información
    foreach ($integrantes as $integrante) {
        echo '<div class="card mb-3">';
        echo '<div class="card-body d-flex align-items-center">'; // Utilizar flexbox para alinear elementos
        echo '<i class="fas fa-user-circle fa-4x mr-3"></i>'; // Icono de usuario al lado izquierdo
        echo '<div>';
        echo '<h5 class="card-title">' . $integrante['nombre'] . '</h5>';
        echo '<p class="card-text">Rol: ' . $integrante['rol'] . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    ?>

</div>

<?php
include_once "footer.php";
?>
