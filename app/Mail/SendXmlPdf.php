<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendXmlPdf extends Mailable
{
    use Queueable, SerializesModels;

    public $xml, $pdf64;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($xml, $pdf64)
    {
        $this->xml = $xml;
        $this->pdf = base64_decode($pdf64);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.emailPrueba')
                    ->attachData($this->xml, 'xml.xml')
                    ->attachData($this->pdf, 'pdf.pdf', ['mime' => 'application/pdf',]);
    }
}
