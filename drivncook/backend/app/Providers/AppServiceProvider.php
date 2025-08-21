<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Personnalisation unique du mail de vérification
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            if (app()->getLocale() === 'en') {
                // Version EN
                return (new MailMessage)
                    ->subject("Activate your Driv'n Cook account")
                    ->greeting('Welcome 👋')
                    ->line("Thanks for signing up to Driv'n Cook.")
                    ->line('To finish creating your account, click the button below.')
                    ->action('Activate my account', $url)
                    ->salutation('— Driv’n Cook Team');
            }

            // Version FR (par défaut)
            return (new MailMessage)
                ->subject("Activez votre compte Driv'n Cook")
                ->greeting('Bienvenue 👋')
                ->line("Merci pour votre inscription sur Driv'n Cook.")
                ->line('Pour finaliser la création de votre compte, cliquez sur le bouton ci-dessous.')
                ->action('Activer mon compte', $url)
                ->line("Si vous n’êtes pas à l’origine de cette inscription, ignorez simplement cet email.")
                ->salutation('— Équipe Driv’n Cook');
        });
    }
}
