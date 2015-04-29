<?php

// Archivo de configuracion
require 'conf/conf.php';
// Configuracion inicial, crea plantillas y demas recursos utilizados
require RUTA_GENERADOR_CODIGO . '/includes/base.inc.php';

// Respuesta devuelta
$rpta = array();
// Ruta del archivo
$rutaArchivo = '';
// Log del archivo cargado por el usuario
miLog($_FILES);

if (isset($_FILES['file'])) {
    $rutaTemporal = $_FILES['file']['tmp_name'];
    $rutaArchivo = RUTA_GENERADOR_CODIGO . '/archivos/' . $_FILES['file']['name'];
    if (!move_uploaded_file($rutaTemporal, $rutaArchivo)) {
        $rpta['error'] = 'Error moviendo archivo: $rutaArchivo';
    }
    // Extraer la extension del nombre del archivo
    $extension = explode('.', $_FILES['file']['name']);
    $tipoArchivo = end($extension);
} else {
    // No es un acceso valido al recurso
    header('Location: index.html');
}

miLog('Procesando: ' . $_FILES['file']['name']);

try {

    switch ($tipoArchivo) {
        case 'xls':
            // Excel 97-2003
        case 'xlsx':
            // Excel >= 2007
        case 'ods':
            // Formatos abiertos de hojas de calculo
            // Librerias para la lectura de las hojas de calculo
            require RUTA_GENERADOR_CODIGO . '/includes/SpreadsheetReader/php-excel-reader/excel_reader2.php';
            require RUTA_GENERADOR_CODIGO . '/includes/SpreadsheetReader/SpreadsheetReader.php';

            // Inicializar libro
            $hojas = new SpreadsheetReader($rutaArchivo);
            // Extraer hojas
            $cadaHoja = $hojas->Sheets();
            // Para las hoja de calculo excel 97 el numero de la fila no inicia en la posicion 0, se deja en la posicion - 1
            $restar = ('xls' == strtolower($tipoArchivo)) ? 1 : 0;
            // Recorre cada una de las hojas
            foreach ($cadaHoja as $numeroHoja => $nombreHoja) {
                if ($nombreHoja == 'ZeroCode') {
                    // La hoja de configuracion no la tiene en cuenta
                    continue;
                }
                // Identificador de la hoja
                $idHoja = reemplazarCaracteresEspeciales(strtolower($nombreHoja));
                // Numero de hoja en proceso
                $hojas->ChangeSheet($numeroHoja);

                // Crear un archivo XML por cada hoja
                $xml = insertarEspacios(0) . '<?xml version="1.0" encoding="UTF-8"?>' . FIN_DE_LINEA;
                $xml .= insertarEspacios(0) . '<crear>' . FIN_DE_LINEA;
                $xml .= insertarEspacios(4) . '<' . $idHoja . '>' . FIN_DE_LINEA;

                // Cada fila
                foreach ($hojas as $numeroFila => $columnas) {
                    $numeroFila -= $restar; 
                    if ($numeroFila > 2 && $columnas[0] !== '') {
                        $idCampo = reemplazarCaracteresEspeciales(strtolower($columnas[1]));
                        $xml .= insertarEspacios(8) . '<' . $idCampo . '>' . FIN_DE_LINEA;
                    }
                    foreach ($columnas as $numeroColumna => $contenido) {
                        if ($contenido == '') {
                            // No tiene contenido
                            continue;
                        }
                        $info = $numeroFila . ':' . $numeroColumna . ' = ' . $contenido . "\n";
                        // Almacena la informacion del archivo en el log
                        miLog($info);
                        switch ($numeroFila) {
                            case 0:
                            // Descripciones caracteristicas formulario
                            case 2:
                                // Descripciones caracteristicas campos del formulario
                                $tag[$numeroColumna] = reemplazarCaracteresEspeciales(strtolower($contenido));
                                break;
                            case 1:
                                // Detalles del encabezado
                                $xml .= insertarEspacios(8) . '<' . $tag[$numeroColumna] . '>' . $contenido . '</' . $tag[$numeroColumna] . '>' . FIN_DE_LINEA;
                                break;
                            default:
                                // Detalles de los campos
                                $xml .= insertarEspacios(12) . '<' . $tag[$numeroColumna] . '>' . $contenido . '</' . $tag[$numeroColumna] . '>' . FIN_DE_LINEA;
                                break;
                        }
                    }
                    if ($numeroFila > 2 && $columnas[0] !== '') {
                        $xml .= insertarEspacios(8) . '</' . $idCampo . '>' . FIN_DE_LINEA;
                    }
                }
                $xml .= insertarEspacios(4) . '</' . $idHoja . '>' . FIN_DE_LINEA;
                $xml .= insertarEspacios(0) . '</crear>' . FIN_DE_LINEA;
                // Crear el archivo XML por cada hoja
                file_put_contents('xml/' . $idHoja . '.xml', $xml);
            }
            break;
        case 'txt':
            // Texto plano
        case 'zcs':
            // Sintaxis ZC
            require RUTA_GENERADOR_CODIGO . '/includes/SintaxixZC/zcs.inc.php';
            $zcl = new zcs($rutaArchivo);
            $rpta['error'] = $zcl->devolverError();
            break;
        default:
            $rpta['error'] = 'Formato (' . $tipoArchivo . ') no soportado.';
            break;
    }
    
    /**
     * Crea los formularios, depende de los archivos xml recien creados
     */
    $nf = new procesarXML();
    $nf->cargarArchivosXML('xml');
} catch (Exception $e) {
    $rpta['error'] = 'Error: ' . $e->getMessage();
}


echo json_encode($rpta);
