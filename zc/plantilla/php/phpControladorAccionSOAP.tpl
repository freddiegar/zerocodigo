public function {_nombreAccion_}() {
        // Valida que el usuario este logueado
        $this->validarSesion();
        if (!$this->input->is_ajax_request()) {
            // No es un llamado valido (no es desde un Ajax, sino por la url)
            redirect('404');
        }
{_asignacionCliente_}
        if (isset($rpta['error']) && count($rpta['error']) > 0) {
            // Pasa al final
        } else if ($datos['accion'] == '{_nombreAccion_}') {
            // Hace el llamado al WS {_nombreAccion_}
            $rpta = $this->modelo->{_nombreAccion_}Cliente($datos);
{_comandoEspecial_}
        } else if (isset($datos['accion'])) {
            $rpta['error'] = 'Error, datos inesperados';
        }
        echo json_encode($rpta, true);
    }
