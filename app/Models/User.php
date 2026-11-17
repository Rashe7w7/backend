<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\EspecificacionIndicador;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profesor_id',
    ];

    public const ROLES = [
        'administrador',
        'ppa',
        'jefe_departamento',
        'decano',
        'rector',
        'vicerrector',
        'secretaria',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const INDICATOR_SPECIFICATIONS_BY_ROLE = [
        'ppa' => [EspecificacionIndicador::Grupo->value],
        'jefe_departamento' => [
            EspecificacionIndicador::Departamento->value,
            EspecificacionIndicador::Grupo->value,
        ],
        'decano' => [
            EspecificacionIndicador::Carrera->value,
            EspecificacionIndicador::NivelAcademico->value,
        ],
        'rector' => [EspecificacionIndicador::Facultad->value],
        'vicerrector' => [EspecificacionIndicador::Facultad->value],
    ];

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }

    public function isAdministrator(): bool
    {
        return $this->role === 'administrador';
    }

    public function canWriteAcademicData(): bool
    {
        return in_array($this->role, ['administrador', 'jefe_departamento'], true);
    }

    /**
     * @return list<string>
     */
    public function allowedIndicatorSpecifications(): array
    {
        if ($this->isAdministrator() || $this->role === 'secretaria') {
            return array_map(
                fn (EspecificacionIndicador $specification): string => $specification->value,
                EspecificacionIndicador::cases(),
            );
        }

        return self::INDICATOR_SPECIFICATIONS_BY_ROLE[$this->role] ?? [];
    }

    public function canManageIndicatorSpecification(EspecificacionIndicador|string|null $specification): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $value = $specification instanceof EspecificacionIndicador
            ? $specification->value
            : $specification;

        return $value !== null
            && in_array($value, self::INDICATOR_SPECIFICATIONS_BY_ROLE[$this->role] ?? [], true);
    }

    public function canManageAnyIndicator(): bool
    {
        return $this->isAdministrator()
            || (self::INDICATOR_SPECIFICATIONS_BY_ROLE[$this->role] ?? []) !== [];
    }

    public function canViewStudentHelpers(): bool
    {
        return in_array($this->role, ['administrador', 'ppa'], true);
    }

    public function canManageStudentHelpers(): bool
    {
        return $this->canViewStudentHelpers();
    }

    public function canDeleteStudentHelpers(): bool
    {
        return $this->isAdministrator();
    }

    public function departmentIds(): Collection
    {
        if (! $this->profesor_id) {
            return collect();
        }

        return JefeDepartamento::query()
            ->where('profesor_id', $this->profesor_id)
            ->where('fecha_inicio', '<=', now())
            ->where(fn ($query) => $query->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()))
            ->pluck('departamento_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
