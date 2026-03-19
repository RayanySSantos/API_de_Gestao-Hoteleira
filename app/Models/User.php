<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    /**
     * Representam os dados básicos do usuário no sistema.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Campos que devem ser ocultados quando o modelo for convertido
     * para array ou JSON (ex: respostas de API).
     */
    protected $hidden = [
        'password', // Oculta a senha por segurança
        'remember_token', // Token utilizado para manter sessão ativa
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', //transforma em data manipulável
            'password' => 'hashed', //criptografa automaticamente da senha
        ];
    }
}
