<?php

class {_nombreModelo_} extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library(array('nusoap', 'session'));
        $zc_params[0] = '{_nombreModelo_}';
        $this->load->library('zc', $zc_params);
    }

{_llamadosModelo_}
    {_validacionModelo_}
    /**
     * Hace el llamado al ws, segun los parametros dados
     * @param string $login Nombre de usuario para loguearse en el sistema
     * @param string $clave Clave para logueo en el sistema
     * @param string $serverURL 
     * @param string $serverScript
     * @param string $metodoALlamar
     * @param array $parametros
     * @return array Respuesta del WS
     */
    function llamarWS($login, $clave, $serverURL, $serverScript, $metodoALlamar, $parametros) {
        return $this->zc->llamarWS($login, $clave, $serverURL, $serverScript, $metodoALlamar, $parametros);
    }

    /**
     * Valida los filtros (condiciones SQL) aplicados en el formulario de busqueda
     * @param string $campos Filtros de busqueda seleccionados por el cliente
     * @param string $accion Accion solicitado por parte del cliente
     * @return array Respuesta de la validacion de datos
     */
    function validarFiltros($campos, $accion){
        return $this->zc->validarFiltros($campos, $accion);
    }
}