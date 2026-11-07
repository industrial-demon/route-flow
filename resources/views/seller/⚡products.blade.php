<?php

use Livewire\Component;

new class extends Component
{
     public $data = [1,2,3];

     public function render()
    {
        return $this->view()->layout('seller::layouts.root', ['title' => __('Seller')]);
    }
};
?>

<div>
    {{json_encode($data)}}
    {{-- It is never too late to be what you might have been. - George Eliot --}}
</div>