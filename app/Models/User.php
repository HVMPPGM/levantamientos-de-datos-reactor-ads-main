<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuarios';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Nombres',
        'ApellidosPat',
        'ApellidoMat',
        'Telefono',
        'Correo',
        'Contrasena',
        'Rol',
        'Estatus',
        'Permisos',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Contrasena',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_registro' => 'datetime',
        'ultima_actividad' => 'datetime',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuarios';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the email address for the user.
     */
    public function getEmailForPasswordReset()
    {
        return $this->Correo;
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->Estatus === 'Activo';
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->Rol === 'Admin';
    }

    /**
     * Verificar si el usuario tiene permisos especiales
     */
    public function hasSpecialPermissions()
    {
        return $this->Permisos === 'si';
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->Nombres} {$this->ApellidosPat} {$this->ApellidoMat}");
    }
}