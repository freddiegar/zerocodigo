<?php

/**
 * Define el fin de linea para los archivos creados
 */
define("FIN_DE_LINEA", "\n");
/**
 * Fin de linea para los archivos html
 */
define('FIN_DE_LINEA_HTML', '<br/>');

/**
 * Clase para la creacion de cajas (text, password, textarea)
 */
require RUTA_GENERADOR_CODIGO . '/modelo/caja.class.php';

/**
 * Clase para la creacion de botones (button)
 */
require RUTA_GENERADOR_CODIGO . '/modelo/boton.class.php';

/**
 * Clase para la creacion de listas (select)
 */
require RUTA_GENERADOR_CODIGO . '/modelo/lista.class.php';

/**
 * Clase para la creacion de unica seleccion (radio)
 */
require RUTA_GENERADOR_CODIGO . '/modelo/radio.class.php';

/**
 * Clase para la creacion de selecciones multiples (checkbox)
 */
require RUTA_GENERADOR_CODIGO . '/modelo/checkbox.class.php';

/**
 * Clase para la creacion de acciones segun el boton seleccionado
 */
require RUTA_GENERADOR_CODIGO . '/modelo/accion.class.php';

/**
 * Crear formulario html
 */
class formulario {

    /**
     * Tipo de WS a crear rest|soap, por defecto = rest
     * @var string
     */
    public $_tipoWS = ZC_WS_REST;

    /**
     * Identificador/Nombre del formulario creado
     * @var string
     */
    public $_id = '';

    /**
     * Metodo a utilizar para el envio del formulario, por defecto POST
     * GET, POST
     * @var string
     */
    public $_metodo = 'POST';

    /**
     * Alamcena las acciones que se crearan en el controlador, debe estar habilitado
     * la creacion de acciones en el servidor.
     * @var string
     */
    public $_accionServidor = '';

    /**
     * Ruta completa de la ruta salida del formulario
     * @var string
     */
    protected $_salidaFormulario = '';

    /**
     * Directorio (carpeta) de salida del formulario
     * @var string
     */
    protected $_rutaFormulario = '';

    /**
     * Almacena cada uno de los elementos creados, se dejan en el orden de creacion
     * @var array
     */
    private $_formulario = array();

    /**
     * Resultado de unior todos los elementos del software
     * @var string
     */
    private $_textoFormulario = '';

    /**
     * Archivos javascript utilizados por el formulario
     * @var string
     */
    private $_js = '';

    /**
     * Almacena la plantilla html de la vista seleccionada
     * @var \plantilla
     */
    private $_plantillaHTML = null;

    /**
     * Elementos utilizados por el formulario, pueden ser text, select, radio, checkbox.
     * @var type
     */
    private $_elementos = array();

    /**
     * Acciones utilizados por el formulario, botones normalmente
     * @var type
     */
    private $_acciones = array();

    /**
     * Asignacion de variables en funciones javascript las usadas en el envio Ajax
     * @var string
     */
    private $_asignacionCliente = '';

    /**
     * Parametros pasados al servidor SOAP, se usan durante la definicion de los parametros que acepta la funcion servidor SOAP
     * @var string
     */
    private $_asignacionParametrosServidorSOAP = '';

    /**
     * Parametros pasados por el cliente segun los valores del Ajax, a ser enviados al servidor SOAP
     * @var string
     */
    private $_asignacionParametrosClienteSOAP = '';

    /**
     * Lista de parametros pasados a la funcion en el servidor SOAP
     * @var string
     */
    private $_asignacionParametrosFuncionServidorSOAP = '';

    /**
     * Lista de paramettros pasados a la funcion de servidor SOAP
     * @var string
     */
    private $_asignacionParametrosFuncionClienteSOAP = "\$datos['accion'] = \$this->input->post('accion');\n";

    /**
     * Inicializacion de parametros pasados al ajax
     * @var string
     */
    private $_inicializacionCliente = '';

    /**
     * Inicializacion la variable que contiene el codigo de la validacion del lado servidor
     * @var string
     */
    private $_validacion = '';

    /**
     * Inicializacion la variable que contiene el codigo para procesar la respuesta del servidor (SOAP)
     * @var string
     */
    private $_procesarWS = '';

    /**
     * Inicializacion la variable que contiene el codigo de las funcioes creadas
     * @var string
     */
    private $_funciones = '';

    /**
     * Inicializacion la variable que contiene el codigo html de los filtros disponibles
     * @var string
     */
    private $_filtros = '';

    /**
     * Inicializacion la variable que contiene el codigo del llamado a al servidor (SOAP)
     * @var string
     */
    private $_clienteSOAP = '';

    /**
     * Inicializacion la variable que contiene el javascript (con AJAX) que hace uso del modelo en el servidor
     * @var string
     */
    private $_accionCliente = '';

    /**
     * Variable para definir las acciones que se ejecutaran en el servidor WS
     * @var string
     */
    private $_accionesServidorWS = '';

    /**
     * Nombre del archivo controlador del servidor de WS creado en /controllers
     * @var string
     */
    private $_nombreArchivoServidor = '';

    /**
     * Nombre del archivo modelo creado en /models
     * @var string
     */
    private $_nombreArchivoModelo = '';

    /**
     * Nombre del archivo controlador creado en /controllers
     * @var string
     */
    private $_nombreArchivoControlador = '';

    /**
     * Nombre del archivo vista creado en /views
     * @var string
     */
    private $_nombreArchivoVista = '';

    /**
     * Nombre del archivo vista creado en /views
     * @var string
     */
    private $_nombreArchivoListar = '';

    /**
     * Nombre del archivo javascrip relacionado al formulario
     * @var type
     */
    private $_nombreArchivoJs = '';
    
    /**
     * Nombre de la funcion que hace la validacion de datos
     * @var type
     */
    private $_nombreFuncionValidacion = '';

    /**
     * Funcion de inicializacion del formulario, reqeuire que seha suministradas
     * unos datos basicos relacionados al formulario
     * @param array $caracteristicas
     * @throws Exception
     */
    function __construct($caracteristicas) {
        if (!is_array($caracteristicas)) {
            throw new Exception(__FUNCTION__ . ": Y las caracteristicas del formulario!?");
        } else {
            $this->_id = ucwords($caracteristicas[ZC_ID]);
            $this->_tipoWS = (isset($caracteristicas[ZC_TIPO_WS])) ? strtolower($caracteristicas[ZC_TIPO_WS]) : strtolower($this->_tipoWS);
            $this->_metodo = (isset($caracteristicas[ZC_FORMULARIO_METODO])) ? strtoupper($caracteristicas[ZC_FORMULARIO_METODO]) : $this->_metodo;
            $this->inicio();
        }
    }

    /**
     * Define nombres de archivos utilizados por las plantillas a lo largo del proceso
     * Define nombres de los archivos creados: modelo, vista, controlador
     * Los nombres se manejan en minuscula y seperados con underscore segun
     * recomendacion de CodeIgniter
     * @return \formulario
     */
    private function inicio() {
        $id = strtolower($this->_id);
        // Nombre vista
        $this->_nombreArchivoVista = 'vista_' . $id;
        // Nombre modelo
        $this->_nombreArchivoModelo = 'modelo_' . $id;
        // Nombre modelo
        $this->_nombreArchivoControlador = $id;
        // Nombre del listado
        $this->_nombreArchivoListar = 'vista_listar_' . $id;
        // Nombre del archivo que guarda las funcionalidades del servidor 
        $this->_nombreArchivoServidor = $id . '_' . $this->_tipoWS;
        // Nombre javascript
        $this->_nombreArchivoJs = $id;
        // Nombre de la funcion de validacion
        $this->_nombreFuncionValidacion = 'validacion' . $this->_id;

        return $this;
    }

    /**
     * Muestra en pantalla el formulario creado
     */
    public function imprimir() {
        echo $this->unirElementosFormulario($this->_formulario);
    }

    /**
     * Retorno en un string los elementos del formulario concatenado
     * @return string
     */
    public function devolver() {
        return $this->_plantillaHTML->devolverPlantilla();
    }

    /**
     * Crear el archivo con el modelo (model) para el formulario
     * @param string $directorioSalida ruta donde se creara el archivo
     * @param string $extension Extension del archivo creado. por defecto PHP
     * @param array $opciones Opciones a aplicar a la plantilla creada
     * @return \formulario
     */
    private function crearModeloFormulario($directorioSalida = '../application/models', $extension = 'php', $opciones = array()) {
        /**
         * Plantilla para el modelo (model)
         */
        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpModeloSOAP.tpl');

        $plantilla->asignarEtiqueta('nombreModelo', $this->_nombreArchivoModelo);
        $plantilla->asignarEtiqueta('nombreFormulario', $this->_id);
        $plantilla->asignarEtiqueta('clienteWS', $this->_clienteSOAP);
        $plantilla->asignarEtiqueta('procesarWS', $this->_procesarWS);
        $plantilla->asignarEtiqueta('funciones', $this->_funciones);
        $plantilla->asignarEtiqueta('validacion', $this->_validacion);
        $plantilla->asignarEtiqueta('nombreValidacion', $this->_nombreFuncionValidacion);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $plantilla->minimizarPlantilla();
        }

        $plantilla->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoModelo);

        return $this;
    }

    /**
     * Crear un arhivo fisico para el el formulario en el directorio de salida definido
     * por defecto:
     *  $directorioSalida = ../application/views (Ruta de salida)
     *  $extension = html (Extension)
     * @param string $directorioSalida Ruta donde se creara el archivo
     * @param string $extension Extension del archivo creado
     * @param array $opciones Opciones a aplicar a la plantilla del formulario creado, minimizar, abrir
     * @return \formulario
     * @throws Exception
     */
    public function crearVistaFormulario($directorioSalida = '../application/views', $extension = 'html', $opciones = array()) {
        /**
         * Plantilla para la vista (view), se puede devolver, por eso se deja en una variable $this
         */
        $this->_plantillaHTML = new plantilla();
        $this->_plantillaHTML->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/html/htmlFluid.tpl');

        $this->_plantillaHTML->asignarEtiqueta('nombreFormulario', $this->_id);
        $this->_plantillaHTML->asignarEtiqueta('metodoFormulario', $this->_metodo);
        $this->_plantillaHTML->asignarEtiqueta('contenidoFormulario', $this->unirElementosFormulario($this->_formulario));
        $this->_plantillaHTML->asignarEtiqueta('archivoJavascript', $this->_js);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $this->_plantillaHTML->minimizarPlantilla();
        }

        $this->_textoFormulario = (0 == count($this->_formulario)) ? '<vacio>' : $this->_plantillaHTML->devolverPlantilla();

        $this->_plantillaHTML->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoVista);

        if (isset($opciones['abrir']) && $opciones['abrir'] === true) {
            $this->_plantillaHTML->abrirPlantilla();
        }

        return $this;
    }

    /**
     * Crear un arhivo fisico para el el formulario en el sirectorio de salida definido
     * por defecto:
     *  $directorioSalida = dist/form (Ruta de salida)
     *  $tipoSalida = php (Extension)
     * @param string $directorioSalida Ruta donde se crear el archivo
     * @param string $extension Extension del archivo creado
     * @param array $opciones Opciones a aplicar a la plantilla del formulario creado
     * @return \formulario
     * @throws Exception
     */
    public function crearListarFormulario($directorioSalida = '../application/views', $extension = 'html', $opciones = array()) {
        /**
         * Plantilla para la vista (view), se puede devolver, por eso se deja en una variable $this
         */
        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/html/htmlListarFluid.tpl');

        $plantilla->asignarEtiqueta('nombreFormulario', $this->_id);
        $plantilla->asignarEtiqueta('metodoFormulario', $this->_metodo);
        $plantilla->asignarEtiqueta('contenidoFormulario', $this->_filtros);
        $plantilla->asignarEtiqueta('archivoJavascript', $this->_js);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $plantilla->minimizarPlantilla();
        }

        $this->_textoFormulario = (0 == count($this->_formulario)) ? '<vacio>' : $plantilla->devolverPlantilla();

        $plantilla->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoListar);

        if (isset($opciones['abrir']) && $opciones['abrir'] === true) {
            $plantilla->abrirPlantilla();
        }

        return $this;
    }

    /**
     * Crea el controlador del formulario del tipo CodeIgniter
     * @param type $directorioSalida Ruta donde se creara el archivos
     * @param type $extension Extension con la cual se creara el archivo
     * @param type $opciones Opciones a aplicar a la plantilla creada
     * @return \formulario
     */
    private function crearControladorFormulario($directorioSalida = '../application/controllers', $extension = 'php', $opciones = array()) {
        /**
         * Plantilla para el controlador (controller)
         */
        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpControladorSOAP.tpl');

        $plantilla->asignarEtiqueta('nombreControlador', $this->_nombreArchivoControlador);
        $plantilla->asignarEtiqueta('nombreVista', $this->_nombreArchivoVista . '.html');
        $plantilla->asignarEtiqueta('nombreVistaListar', $this->_nombreArchivoListar . '.html');
        $plantilla->asignarEtiqueta('nombreModelo', $this->_nombreArchivoModelo);
        $plantilla->asignarEtiqueta('accionServidor', $this->_accionServidor);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $plantilla->minimizarPlantilla();
        }

        $plantilla->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoControlador);

        return $this;
    }

    /**
     * Crea el el archivo javascrip que jace el llamado al modelo para procesar los datos
     * @param type $directorioSalida Ruta donde se creara el archivos
     * @param type $extension Extension con la cual se creara el archivo
     * @param type $opciones Opciones a aplicar a la plantilla creada
     * @return \formulario
     */
    private function crearJavascriptFormulario($directorioSalida = '../publico/js', $extension = 'js', $opciones = array()) {
        /**
         * Plantilla para el manejo de javascript
         */
        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/js/jsInicializacionjQuery.js');

        $plantilla->asignarEtiqueta('nombreFormulario', $this->_id);
        $plantilla->asignarEtiqueta('accionesCliente', $this->_accionCliente);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $plantilla->minimizarPlantilla();
        }

        $plantilla->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoJs);
        // Agregar archivo creado al javascript al formulario
        $this->javascriptFormulario($plantilla->_salidaPlantilla);

        return $this;
    }

    /**
     * Crea el archivo controlador que manejan las funciones de WS del lado servidor
     * @param string $directorioSalida Ruta de salida donde se creara el archivo
     * @param string $extension Extension que tendra el archivo de salida
     * @param array $opciones Opciones de configuracion del arhcivo creado
     * @return \formulario
     */
    private function crearControladorServidorFormulario($directorioSalida = '../application/controllers', $extension = 'php', $opciones = array()) {
        /**
         * Plantilla para el modelo (model)
         */
        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpControladorServidorSOAP.tpl');

        $plantilla->asignarEtiqueta('nombreControlador', $this->_nombreArchivoServidor);
        $plantilla->asignarEtiqueta('accionesServidorWS', $this->_accionesServidorWS);

        if (isset($opciones['minimizar']) && $opciones['minimizar'] === true) {
            $plantilla->minimizarPlantilla();
        }

        $plantilla->crearPlantilla($directorioSalida, $extension, $this->_nombreArchivoServidor);

        return $this;
    }

    /**
     * Crea las cajas de texto en el formulario, segun las caracteristicas dadas
     * @return \formulario
     */
    public function agregarCajaFormulario() {
        $cajas = func_get_args();
        $this->agregarElementoFormulario($cajas);
        return $this;
    }

    /**
     * Crea los botones de accion definidos por el usuario segun las caracteristicas dadas
     * @return \formulario
     */
    public function agregarBotonesFormulario() {
        $botones = func_get_args();
        $this->agregarElementoFormulario($botones);
        return $this;
    }

    /**
     * Alias de la funcion para agregar elementos desde otras clases
     * @param type $elementos
     */
    public function agregarElementoDesconocidoFormulario() {
        $elementos = func_get_args();
        $this->agregarElementoFormulario($elementos);
    }

    /**
     * Crea los elemntos dentro del formulario segun las caracteristicas entregadas
     * @param array $elementos Caracteristicas de los elementos a entregar
     * @throws Exception
     * @return \formulario
     */
    private function agregarElementoFormulario($elementos) {
        foreach ($elementos as $caracteristicas) {
            if (!is_array($caracteristicas)) {
                throw new Exception(__FUNCTION__ . ": Y las caracteristicas del elemento!?");
            }
            /**
             * Se valida en minuscula para evitar ambiguaedades: Boton, boton, BOTON, etc
             * Se debe dejar, en este punto no ha pasado por la funcion elementos::verificar
             */
            $caracteristicas[ZC_ELEMENTO] = (strtolower($caracteristicas[ZC_ELEMENTO]));
            switch ($caracteristicas[ZC_ELEMENTO]) {
                case ZC_ELEMENTO_CAJA_TEXTO:
                    $this->agregarElementoCajaFormulario($caracteristicas);
                    break;
                case ZC_ELEMENTO_RADIO:
                    $this->agregarElementoRadioFormulario($caracteristicas);
                    break;
                case ZC_ELEMENTO_CHECKBOX:
                    $this->agregarElementoCheckboxFormulario($caracteristicas);
                    break;
                case ZC_ELEMENTO_SELECT:
                    $this->agregarElementoListaFormulario($caracteristicas);
                    break;
                case ZC_ELEMENTO_RESTABLECER:
                case ZC_ELEMENTO_CANCELAR:
                case ZC_ELEMENTO_BOTON:
                case ZC_ACCION_AGREGAR:
                case ZC_ACCION_BUSCAR:
                case ZC_ACCION_MODIFICAR:
                case ZC_ACCION_BORRAR:
                    $this->agregarElementoBotonFormulario($caracteristicas);
                    break;
                default:
                    throw new Exception(__FUNCTION__ . ": Tipo de elemento no definido: {$caracteristicas[ZC_ELEMENTO]}!");
            }
        }
        return $this;
    }

    /**
     * Asigna las caracteristicas que tendra el formulario
     * @return \formulario
     */
    public function inicioFormulario() {
        return $this;
    }

    /**
     * Construye los archivos necesarios.
     * @return \formulario
     */
    public function finFormulario() {
        $this->procesarFormulario();
        $this->crearControladorFormulario();
        $this->crearModeloFormulario();
        $this->crearControladorServidorFormulario();
        // Las acciones del cliente se deben procesar despues de crear el controlador,
        // ya que este ultimo hace referencia a la URL
        $this->crearAccionesClienteFormulario();
        $this->crearJavascriptFormulario();
        // La vista se debe crear despues de los archivos javascript (cargar bien rutas js)
        $this->crearVistaFormulario();
        // La vista de busqueda y listado de campos de la tabla
        $this->crearListarFormulario();
        $this->fin();
        return $this;
    }

    /**
     * Une los elmentos de un formulario
     * @param array $elementos Elementos del formlario
     * @return string
     */
    private function unirElementosFormulario($elementos) {
        $elementosFormulario = '';
        foreach ($elementos as $elemento => $valor) {
            // Hasta el momento solo se utilizan para los botones
            $estiloInicio = $estiloFin = '';
            if ($elemento == 'acciones') {
                // Las acciones son muchas en una sola fila, se agrupan al final del proceso
                $estiloInicio = "
                    <div class='row'>
                        <div class='col-md-1'></div>
                        <div class='col-md-5'>
                            <div class='text-right'>
                        ";
                $estiloFin = "
                            </div>
                        </div>
                        <div class='col-md-5'></div>
                        <div class='col-md-1'></div>
                    </div>
                ";
            }
            if (is_array($elementos[$elemento])) {
                $elementosFormulario .= $estiloInicio . $this->unirElementosFormulario($elementos[$elemento]) . $estiloFin;
            } else {
                $elementosFormulario .= $elementos[$elemento];
            }
        }
        return $elementosFormulario;
    }

    /**
     * Agrega las botones dentro del formulario, segun caracteristicas
     * @param string $caracteristicas Caracteristicas extraidas del XML
     * @param string $tipoAccion Tipo de acciones boton|restablecer
     * @return \formulario
     */
    private function agregarElementoBotonFormulario($caracteristicas, $tipoAccion = 'boton') {
        $html = new boton($caracteristicas, $tipoAccion);
        $html->crear();
        // Devuelve el elemento, no usa devoverl() t a que los botones no usan la plantilla
        $this->_formulario['acciones'][$html->_prop[ZC_ID]] = $html->devolverElemento();
        $this->_acciones[] = $html->_prop;
        return $this;
    }

    /**
     * Agrega las cajas de texto dentro del formulario, segun caracteristicas
     * @param string $caracteristicas Caracteristicas extraidas del XML
     * @return \formulario
     */
    private function agregarElementoCajaFormulario($caracteristicas) {
        $html = new caja($caracteristicas);
        $html->crear();
        $this->_formulario['elementos'][$html->_prop[ZC_ID]] = $html->devolver();
        $this->_elementos[] = $html->_prop;
        return $this;
    }

    /**
     * Agrega las listas dentro del formulario, segun caracteristicas
     * @param string $caracteristicas
     * @return \formulario
     */
    private function agregarElementoListaFormulario($caracteristicas) {
        $html = new lista($caracteristicas);
        $html->crear();
        $this->_formulario['elementos'][$html->_prop[ZC_ID]] = $html->devolver();
        $this->_elementos[] = $html->_prop;
        return $this;
    }

    /**
     * Agrega las radios dentro del formulario, segun caracteristicas
     * @param string $caracteristicas
     * @return \formulario
     */
    private function agregarElementoRadioFormulario($caracteristicas) {
        $html = new radio($caracteristicas);
        $html->crear();
        $this->_formulario['elementos'][$html->_prop[ZC_ID]] = $html->devolver();
        $this->_elementos[] = $html->_prop;
        return $this;
    }

    /**
     * Agrega las checkbox dentro del formulario, segun caracteristicas
     * @param string $caracteristicas
     * @return \formulario
     */
    private function agregarElementoCheckboxFormulario($caracteristicas) {
        $html = new checkbox($caracteristicas);
        $html->crear();
        $this->_formulario['elementos'][$html->_prop[ZC_ID]] = $html->devolver();
        $this->_elementos[] = $html->_prop;
        return $this;
    }

    /**
     * Agrea los archivos javascript al formulario
     * @return \formulario
     */
    public function javascriptFormulario() {
        $javascript = func_get_args();
        $this->agregarJavascriptFormulario($javascript);
        return $this;
    }

    /**
     * Define el fin del documento html
     * @return \formulario
     */
    private function fin() {
        return $this;
    }

    /**
     * Crear archivo encargado de hacer la validaciones del lado servidor
     * $directorioSalida = dist/serv
     * @param string $directorioSalida Ruta de salida del archivo
     * @return \formulario
     * @throws Exception
     */
    private function modeloValidacionFormulario() {
        $validacion = '';
        foreach ($this->_elementos as $nro => $caracteristicas) {

            $this->_inicializacionCliente .= "var {$caracteristicas[ZC_ID]} = \$.trim($('#{$caracteristicas[ZC_ID]}').val());" . FIN_DE_LINEA . insertarEspacios(16);
            $this->_asignacionCliente .= ('' == $this->_asignacionCliente) ? '' : ', ';
            $this->_asignacionCliente .= $caracteristicas[ZC_ID] . ': ' . $caracteristicas[ZC_ID];

            $this->_asignacionParametrosServidorSOAP .= ($this->_asignacionParametrosServidorSOAP == '') ? '' : ',' . FIN_DE_LINEA . insertarEspacios(12);
            $this->_asignacionParametrosServidorSOAP .= "'{$caracteristicas[ZC_ID]}' => 'xsd:{$caracteristicas[ZC_DATO_WS]}'";

            // Los datos se envia codificados para evitar errores con caracteres especiales, ademas
            //permite envial 'cualquier' tipo de dato
            $this->_asignacionParametrosClienteSOAP .= ($this->_asignacionParametrosClienteSOAP == '') ? '' : ',' . FIN_DE_LINEA . insertarEspacios(12);
            $this->_asignacionParametrosClienteSOAP .= ($caracteristicas[ZC_ELEMENTO] != ZC_ELEMENTO_CHECKBOX) ? "'{$caracteristicas[ZC_ID]}' => \$datos['{$caracteristicas[ZC_ID]}']" : "'{$caracteristicas[ZC_ID]}' => json_encode(\$datos['{$caracteristicas[ZC_ID]}'])";

            $this->_asignacionParametrosFuncionServidorSOAP .= ($this->_asignacionParametrosFuncionServidorSOAP == '') ? '' : ', ';
            $this->_asignacionParametrosFuncionServidorSOAP .= "\${$caracteristicas[ZC_ID]}";

            $this->_asignacionParametrosFuncionClienteSOAP .= insertarEspacios(8) . "\$datos['{$caracteristicas[ZC_ID]}'] = \$this->input->post('{$caracteristicas[ZC_ID]}');" . FIN_DE_LINEA;

            // Validacion obligatoriedad
            $validacion .= validarArgumentoObligatorio($caracteristicas[ZC_ID], $caracteristicas[ZC_ETIQUETA], $caracteristicas[ZC_OBLIGATORIO], $caracteristicas[ZC_OBLIGATORIO_ERROR]);
            // Validacion tipo de dato Entero
            $validacion .= validarArgumentoTipoDato($caracteristicas[ZC_ID], $caracteristicas[ZC_ETIQUETA], $caracteristicas[ZC_ELEMENTO], $caracteristicas[ZC_DATO], $caracteristicas[ZC_DATO_ERROR]);
            // Validacion longitud minima del campo
            $validacion .= validarArgumentoLongitudMinima($caracteristicas[ZC_ID], $caracteristicas[ZC_ETIQUETA], $caracteristicas[ZC_DATO], $caracteristicas[ZC_LONGITUD_MINIMA], $caracteristicas[ZC_LONGITUD_MINIMA_ERROR]);
            // Validacion longitud maxima del campo
            $validacion .= validarArgumentoLongitudMaxima($caracteristicas[ZC_ID], $caracteristicas[ZC_ETIQUETA], $caracteristicas[ZC_DATO], $caracteristicas[ZC_LONGITUD_MAXIMA], $caracteristicas[ZC_LONGITUD_MAXIMA_ERROR]);
        }

        $plantilla = new plantilla();
        $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpValidacionServidor.tpl');
        $plantilla->asignarEtiqueta('nombreFormulario', $this->_id);
        $plantilla->asignarEtiqueta('elementosFormulario', $validacion);
        $plantilla->asignarEtiqueta('accionesSinValidacion', ZC_ACCION_SIN_VALIDACION);
        // Concatena cada accion del cliente
        $this->_validacion = $plantilla->devolverPlantilla() . FIN_DE_LINEA;
        return $this;
    }

    /**
     * Crea funcion estandar para el proceso de las respuestas entregadas por el WS
     * @return \formulario
     */
    private function modeloProcesarWS() {
        $plantilla = new plantilla();

        if ($this->_tipoWS == ZC_WS_SOAP) {
            /**
             * Plantilla para procesar las respuesta tipo SOAP
             */
            $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpProcesarSOAP.tpl');
        } elseif ($this->_tipoWS == ZC_WS_REST) {
            /**
             * Plantilla para procesar las respuesta tipo REST
             */
            $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpProcesarREST.tpl');
        }

        // Concatena el tipo de procesamiento segune el tipo de WS
        $this->_procesarWS .= $plantilla->devolverPlantilla() . FIN_DE_LINEA;

        return $this;
    }

    /**
     * Acciones en el servidor
     * @param string $directorioSalida
     * @return \formulario
     */
    private function modeloControladorAcciones() {
        foreach ($this->_acciones as $nro => $caracteristicas) {
            if (in_array($caracteristicas[ZC_ELEMENTO], array(ZC_ELEMENTO_RESTABLECER, ZC_ELEMENTO_CANCELAR))) {
                // Los botones tipo restablecer, cancelar no crean acciones
                continue;
            }
            // Determina la accion a ejecutar en el cvontrolador
            switch ($caracteristicas[ZC_ELEMENTO]) {
                case ZC_ACCION_BUSCAR:
                    $comando = "\$datos['accion'] = \$this->input->post('accion');" . FIN_DE_LINEA . insertarEspacios(8);
                    $comando .= "\$datos['filtros'] = \$this->input->post('filtros');" . FIN_DE_LINEA . insertarEspacios(8);
                    $comando .= str_replace('{_nombreModelo_}', $this->_nombreArchivoModelo, "\$rpta = \$this->{_nombreModelo_}->validarFiltros(\$datos['filtros'], \$datos['accion']);");
                    break;
                default:
                    $comando = $this->_asignacionParametrosFuncionClienteSOAP . insertarEspacios(8);;
                    $comando .= str_replace(array('{_nombreModelo_}', '{_nombreValidacion_}'), array($this->_nombreArchivoModelo, $this->_nombreFuncionValidacion), "\$rpta = \$this->{_nombreModelo_}->{_nombreValidacion_}(\$datos);");
                    break;
            }
            /**
             * Plantilla para la creacion de acciones en el controlador
             */
            $plantilla = new plantilla();
            if ($this->_tipoWS == ZC_WS_SOAP) {
                $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpControladorAccionSOAP.tpl');
                $plantilla->asignarEtiqueta('nombreAccion', $caracteristicas[ZC_ID]);
                // Se crean durante el proceso de los elementos para las validaciones
                $plantilla->asignarEtiqueta('asignacionCliente', $comando);
                $plantilla->asignarEtiqueta('nombreModelo', $this->_nombreArchivoModelo);
            } else if ($this->_tipoWS == ZC_WS_REST) {
                $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpControladorAccionREST.tpl');
            }
            // Concatena cada accion del cliente
            $this->_accionServidor .= FIN_DE_LINEA . insertarEspacios(4) . $plantilla->devolverPlantilla() . FIN_DE_LINEA;
        }
        return $this;
    }

    /**
     * Acciones del lado cliente Ajax
     * @param string $directorioSalida
     * @return \formulario
     */
    private function crearAccionesClienteFormulario() {
        foreach ($this->_acciones as $nro => $caracteristicas) {
            if (in_array($caracteristicas[ZC_ELEMENTO], array(ZC_ELEMENTO_RESTABLECER, ZC_ELEMENTO_CANCELAR))) {
                // Los botones tipo restablecer no crean accciones de envio, ya tiene la
                // accion preferida
                continue;
            }
            switch ($caracteristicas[ZC_ELEMENTO]) {
                case ZC_ACCION_BUSCAR:
                    $tipoPlantilla = 'jsLlamadosBuscarAjax.js';
                    break;
                default:
                    $tipoPlantilla = 'jsLlamadosDefaultAjax.js';
                    break;
            }
            /**
             * Plantilla para los envio con AJAX en javascript (jQuery)
             */
            $plantilla = new plantilla();
            $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/js/' . $tipoPlantilla);
            $plantilla->asignarEtiqueta('nombreAccion', $caracteristicas[ZC_ID]);
            $plantilla->asignarEtiqueta('nombreControlador', $this->_nombreArchivoControlador);
            // Se crean durante el proceso de los elementos para las validaciones
            $plantilla->asignarEtiqueta('inicializacionCliente', $this->_inicializacionCliente);
            $plantilla->asignarEtiqueta('asignacionCliente', $this->_asignacionCliente);
            // Se define en la creacion de la plantilla del controlador
            $plantilla->asignarEtiqueta('nombreFormulario', $this->_id);
            $plantilla->asignarEtiqueta('accionCliente', '//Accion Cliente va aqui');
            $plantilla->asignarEtiqueta('mensajeError', ZC_MENSAJE_ERROR_BUSCAR);

            // Concatena cada accion del cliente
            $this->_accionCliente .= $plantilla->devolverPlantilla() . FIN_DE_LINEA;
        }
        return $this;
    }

    /**
     * Crear cliente WS para el caso de WS SOAP
     * @return \formulario
     */
    private function modeloWsSOAPClienteFormulario() {
        foreach ($this->_acciones as $nro => $caracteristicas) {
            if (in_array($caracteristicas[ZC_ELEMENTO], array(ZC_ELEMENTO_RESTABLECER, ZC_ELEMENTO_CANCELAR))) {
                // Los botones tipo restablecer, cancelar no crean acciones
                continue;
            }
            // Determina los paramettros a definir en el modelo
            switch ($caracteristicas[ZC_ELEMENTO]) {
                case ZC_ACCION_BUSCAR:
                    $comando = "'filtros' => \$datos['filtros'],";
                    break;
                default:
                    $comando = $this->_asignacionParametrosClienteSOAP;
                    break;
            }
            /**
             * Plantilla para la creacion de acciones en el cliente
             */
            $plantilla = new plantilla();
            $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpCrearAccion.tpl');
            $plantilla->asignarEtiqueta('nombreModelo', $this->_id);
            $plantilla->asignarEtiqueta('nombreAccion', $caracteristicas[ZC_ID]);
            $plantilla->asignarEtiqueta('servidorAccion', $this->_nombreArchivoServidor);
            
            $plantilla->asignarEtiqueta('asignacionCliente', $comando);

            // Concatena las acciones que se pueden llamar desde el cliente
            $this->_clienteSOAP .= $plantilla->devolverPlantilla() . FIN_DE_LINEA;
        }
        return $this;
    }

    /**
     * Crear servidor WS de lado servidor
     * @param string $directorioSalida
     * @return \formulario
     */
    private function controladorWsSOAPServidorFormulario() {
        foreach ($this->_acciones as $nro => $caracteristicas) {
            if (in_array($caracteristicas[ZC_ELEMENTO], array(ZC_ELEMENTO_RESTABLECER, ZC_ELEMENTO_CANCELAR))) {
                // Los botones tipo restablecer, cancelar no crean acciones
                continue;
            }
            
            // Determina la accion a ejecutar en el cvontrolador
            switch ($caracteristicas[ZC_ELEMENTO]) {
                case ZC_ACCION_BUSCAR:
                    $asignacionCliente=  "'filtros' => 'xsd:string'";
                    $asignacionFuncion = '$filtros';
                    break;
                default:
                    $asignacionCliente=  $this->_asignacionParametrosServidorSOAP;
                    $asignacionFuncion = $this->_asignacionParametrosFuncionServidorSOAP;
                    break;
            }
            /**
             * Plantilla para la creacion de acciones en el cliente
             */
            $plantilla = new plantilla();
            $plantilla->cargarPlantilla(RUTA_GENERADOR_CODIGO . '/plantilla/php/phpModeloServidorSOAP.tpl');
            $plantilla->asignarEtiqueta('nombreControlador', $this->_nombreArchivoServidor);
            $plantilla->asignarEtiqueta('nombreAccion', $caracteristicas[ZC_ID]);
            $plantilla->asignarEtiqueta('nombreFuncion', $caracteristicas[ZC_ID] . 'Servidor');
            $plantilla->asignarEtiqueta('asignacionCliente', $asignacionCliente);
            $plantilla->asignarEtiqueta('asignacionFuncion', $asignacionFuncion);
            // Asigna la accion del lado servidor
            $accion = new accion($this->_elementos, $this->_id, $caracteristicas[ZC_ELEMENTO]);
            $plantilla->asignarEtiqueta('accionServidor', $accion->crear()->devolverElemento());
            // Concatena las funciones que se ejecutaran en el modelo
            $this->_funciones .= $accion->devolverFuncion();
            // Concatena las los filtros de los formularios de busqueda
            $this->_filtros .= $accion->devolverFiltro();
            // Concatena las acciones que se pueden llamar desde el cliente
            $this->_accionesServidorWS .= FIN_DE_LINEA . insertarEspacios(4) . $plantilla->devolverPlantilla() . FIN_DE_LINEA;
        }
        return $this;
    }

    /**
     * Agrega los archivos javascript manejados por el formulario
     * @param array $javascript
     * @return \formulario
     */
    private function agregarJavascriptFormulario($javascript) {
        $rutaJavascript = (is_array($javascript)) ? $javascript : array($javascript);
        foreach ($rutaJavascript as $ruta) {
            if (!is_file($ruta)) {
                throw new Exception(__FUNCTION__ . 'cwd: ' . getcwd() . ": Ruta de archivo no valida: {$ruta}!?");
            }
            $this->_js .= "<!--Inclusion archivo js -->" . FIN_DE_LINEA . insertarEspacios(8);
            // Cambia la ruta relativa, por una ruta absoluta
            $this->_js .= "<script type='text/javascript' src='" . convertir2UrlLocal($ruta) . "'></script>" . FIN_DE_LINEA . insertarEspacios(8);
        }
        return $this;
    }

    /**
     * Formate la accion a procesar del lado servidor, pasando $ => \$
     * @param string $accion
     * @return string
     */
    private function formatearAccionFormulario($accion) {
        // $accion = str_replace('$', '\\$', $accion);
        return $accion;
    }

    /**
     * Ejecuta todas las acciones para crear los formularios
     */
    private function procesarFormulario() {
        $this->modeloProcesarWS();
        $this->modeloValidacionFormulario();
        $this->modeloWsSOAPClienteFormulario();
        $this->modeloControladorAcciones();
        $this->controladorWsSOAPServidorFormulario();
        return $this;
    }

}
