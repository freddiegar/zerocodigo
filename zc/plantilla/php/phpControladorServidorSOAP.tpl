<?php

// Solo muestra errores fatales
// Necesario para devolver los datos correctos en la respuesta del servidor
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

class {_nombreControlador_} extends CI_Controller{
    function {_nombreControlador_}(){
        parent::__construct();
        // Libreria para el manejo de WS
        $this->load->helper('url');
        $this->load->library('nusoap');
        // Clase para el manejo de Servidor WS
        $this->_miURL = current_url();
        $this->_miSOAPACTION = false;
        $this->_miUSE = 'rpc';
        $this->_miSTYLE = 'encoded';
        $this->_SRV_WS = new soap_server();
        $this->_SRV_WS->configureWSDL('{_nombreControlador_}', $this->_miURL);
        $this->_SRV_WS->wsdl->schemaTargetNamespace = $this->_miURL;
        // Para manejo de caracteres especiales, tildes, &, etc.
        $this->_SRV_WS->soap_defencoding = 'UTF-8';
        $this->_SRV_WS->decode_utf8 = true;
        $this->_SRV_WS->encode_utf8 = true;
        {_comandoEspecial_}
     }

{_accionesServidorWS_}
}