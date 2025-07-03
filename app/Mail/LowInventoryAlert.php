<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowInventoryAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $lowInventoryProducts;
    public $normalInventoryProducts;
    public $Inventorythreshold;
    public $Email;

    public function __construct($lowInventoryProducts, $normalInventoryProducts, $Inventorythreshold, $Email)
    {
        $this->lowInventoryProducts = $lowInventoryProducts;
        $this->normalInventoryProducts = $normalInventoryProducts;
        $this->Inventorythreshold = $Inventorythreshold;
        $this->Email = $Email;
    }

    public function build()
    {
        return $this->subject('Inventory Alert')
                    ->view('emails.low-inventory-alert');
    }
}


