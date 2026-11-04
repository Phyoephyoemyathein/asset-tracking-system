<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Asset;

class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function build()
    {
        return $this->subject('⚠️ Low Stock Warning Alert!')
                    ->view('emails.low-stock');
    }
}