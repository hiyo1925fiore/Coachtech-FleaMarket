<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Exhibition;

class TradeCompleteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $exhibition;
    public $buyerId;

    public function __construct(Exhibition $exhibition, $buyerId)
    {
        $this->exhibition = $exhibition;
        $this->buyerId = $buyerId;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->view('emails.trade_complete');
    }
}
