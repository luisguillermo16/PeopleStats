<?php

// app/View/Components/ModalCrearVotante.php
namespace App\View\Components;

use Illuminate\View\Component;

class ModalCrearVotante extends Component
{
    public $votante;
    public $barrios;
    public $lugares;
    public $concejalOpciones;
    public $lider;
public function __construct($barrios, $lugares, $concejalOpciones, $lider)
{
    $this->barrios = $barrios;
    $this->lugares = $lugares;
    $this->concejalOpciones = $concejalOpciones;
    $this->lider = $lider;
}
    public function render()
    {
        return view('components.ingresarVotantes.modal-crear-votante');
    }
}
