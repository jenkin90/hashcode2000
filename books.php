<?php
error_reporting(E_ALL);
if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
    $fichero = file_get_contents($_FILES['uploadedFile']['tmp_name']);
    $lineas = explode("\n", $fichero);

    [$numeroDeLibros, $numeroDeLibrerias, $numeroDeDias] = explode(' ',$lineas[0]);
    $scoreLibros=explode(' ',$lineas[1]);
    $librerias=array();
    $contadorLibrerias=0;
    $tiemposLibrerias=array();

    for($i=2, $loopsMax = count($lineas); $i< $loopsMax-2; $i += 2){

        [$librerias[$contadorLibrerias]['numLibros'],
            $librerias[$contadorLibrerias]['diasRegistro'],
            $librerias[$contadorLibrerias]['librosDia']]=explode(' ',$lineas[$i]);
        $tiemposLibrerias[$contadorLibrerias]=$librerias[$contadorLibrerias]['diasRegistro'];
        $librerias[$contadorLibrerias]['limiteLibros']=($numeroDeDias-$librerias[$contadorLibrerias]['diasRegistro'])*
                                                        $librerias[$contadorLibrerias]['librosDia'];


        $libros=explode(' ',$lineas[$i+1]);
        $librerias[$contadorLibrerias]['libros']=array();
        foreach ($libros as $libro){
            $librerias[$contadorLibrerias]['libros'][$libro]=$scoreLibros[$libro];
        }
        arsort($librerias[$contadorLibrerias]['libros']);
        $contadorLibrerias++;
    }
    asort($tiemposLibrerias);



    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.substr($_FILES['uploadedFile']['name'],0,1).'.out');
    header('Content-Type: text/plain');
    $separador="\n";
    $contador=0;

    $librosEnviados=array();
    $librosEnvio=array();
    //foreach ($librerias as $idLibrerias => $libreria ) {
    foreach($tiemposLibrerias as $idLibrerias => $tiempo){
        $librosEnvio[$idLibrerias] = array();
        $libreria=$librerias[$idLibrerias];
        $contadorAuxiliar=0;
        foreach ($libreria['libros'] as $libro => $peso) {
            if (in_array($libro, $librosEnviados, true) === false &&
                $contadorAuxiliar<$librerias[$idLibrerias]['limiteLibros']) {
                $librosEnviados[] = $libro;
                $librosEnvio[$idLibrerias][] = $libro;
                $contadorAuxiliar++;
            }
        }

        if(count($librosEnvio[$idLibrerias])>0) {
            $contador=$contador+1;

        }

    }

        echo $contador;
        echo $separador;
    foreach($tiemposLibrerias as $idLibrerias => $tiempo){
    //foreach ($librerias as $idLibrerias => $libreria ) {
        if (count($librosEnvio[$idLibrerias]) > 0) {
            echo $idLibrerias . ' ' . count($librosEnvio[$idLibrerias]);
            echo $separador;
            foreach ($librosEnvio[$idLibrerias] as $libro) {
                echo $libro . ' ';
            }
            echo $separador;
        }
    }



} else {
    ?>
    <form method="POST" action="books.php" enctype="multipart/form-data">
        <input type="file" name="uploadedFile"/>
        <input type="submit" name="uploadBtn" value="Upload"/>
    </form>
    <?php
}
