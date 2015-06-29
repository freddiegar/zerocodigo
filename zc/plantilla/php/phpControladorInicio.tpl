<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class {_nombreControlador_} extends CI_Controller {

    /**
     * Datos de configuracion del controlador
     * @var array
     */
    private $_data = array(
        'formulario' => '{_idFormulario_}',
        'vista' => '{_nombreVista_}',
        'navegacion' => '',
    );

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->_data['navegacion'] = $this->load->view('{_paginaNavegacion_}.html', null, true);
    }

    /**
     * Validar lo solicitud sea valida
     */
    public function validarSesion() {
        if ($this->session->userdata('zc_logueado') !== true) {
            // No esta logueado, pide iniciar sesion
            redirect('zlogin');
        } 
    } 

    /**
     * Accion por defecto
     */
    public function index() {
        $this->inicio();
    }

    /**
     * Pagina de bienvenida
     */
    public function inicio() {
        // Valida que el usuario este logueado
        $this->validarSesion();

        $this->load->view($this->_data['vista'], $this->_data);
    }

}
