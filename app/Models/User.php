<?php

namespace App\Models;

use App\Observers\StatusObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy([StatusObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function generateVerifiedCode(): int
    {
        $code = fake()->randomNumber(6);

        \DB::table("verification_phone_codes")->updateOrInsert(
            ["phone" => $this->phone],
            ["code" => $code]
        );

        return $code;
    }

    public function verifyCode(int $code): bool
    {
        return self::verifyPhoneCode($code, $this->phone);
    }

    static public function verifyPhoneCode(int $code, string $phone): bool
    {
        $codeDB = \DB::table("verification_phone_codes")->where("phone", $phone)->first()->code;

        return $codeDB === $code;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
