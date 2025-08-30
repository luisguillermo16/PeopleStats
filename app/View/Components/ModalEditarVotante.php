<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ModalEditarVotante extends Component
{
    public $votante;
    public $barrios;
    public $lugares;
    public $concejalOpciones;
    public $lider;

    public function __construct($votante, $barrios, $lugares, $concejalOpciones, $lider)
    {
        $this->votante = $votante;
        $this->barrios = $barrios;
        $this->lugares = $lugares;
        $this->concejalOpciones = $concejalOpciones;
        $this->lider = $lider;
    }

    public function render()
    {
        return view('components.ingresarVotantes.modal-editar-votante');
    }
}
