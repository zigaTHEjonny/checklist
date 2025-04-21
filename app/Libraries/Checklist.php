<?php namespace App\Libraries;

class Checklist {
    public function listItem ($item) {
        return view('components/listItem', $item);
    
    }
}