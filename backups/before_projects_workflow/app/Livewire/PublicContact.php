<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // Assuming mail is configured, or we'll simulate
use App\Models\ContactMessage; // Optional: If we want to save to DB

class PublicContact extends Component
{
    public $name;
    public $email;
    public $subject = 'Soporte Técnico';
    public $message;

    public $successMessage;

    // Dynamic Settings
    public $supportWhatsappUrl;
    public $supportPhone;
    public $supportEmail = 'soporte@diogenes.com.mx';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'subject' => 'required',
        'message' => 'required|min:10',
    ];

    public function mount()
    {
        $settings = DB::table('global_settings')
                    ->whereIn('key', ['support_whatsapp_url', 'support_phone'])
                    ->pluck('value', 'key');
        
        $this->supportWhatsappUrl = $settings['support_whatsapp_url'] ?? 'https://wa.me/522281405060';
        $this->supportPhone = $settings['support_phone'] ?? '522281405060';
    }

    public function submit()
    {
        $this->validate();

        // Simulate sending email (or uncomment if Mail is configured)
        // Mail::to('soporte@diogenes.com.mx')->send(new ContactFormMail($this->all()));

        // For now, let's just simulate success and clear form
        $this->successMessage = '¡Gracias por tu mensaje! Hemos recibido tu solicitud y te contactaremos a la brevedad.';
        
        $this->reset(['name', 'email', 'subject', 'message']);
    }

    public function render()
    {
        return view('livewire.public-contact')->layout('layouts.marketing');
    }
}
