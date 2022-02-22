<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Lang;

class SendXmlPdf extends Mailable
{
    use Queueable, SerializesModels;

    public $xml, $pdf64;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($xml, $pdf64, $comercial_name, $folio, $serie)
    {
        $this->xml = $xml;
        $this->pdf = base64_decode($pdf64);
        $this->comercial_name = $comercial_name;
        $this->folio = $folio;
        $this->serie = $serie;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.sendXmlPdf')
                    ->subject(Lang::getFromJson(config('app.name').' - '.$this->comercial_name.' - '.$this->serie.' - '.$this->folio))
                    ->attachData($this->xml, 'xml.xml')
                    ->attachData($this->pdf, 'pdf.pdf', ['mime' => 'application/pdf',]);
    }
}
