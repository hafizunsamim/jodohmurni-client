<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <--- penting
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Str; 

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'users';

    /**
     * Sekarang PK kita UUID (string), bukan auto increment INT.
     */
    public $incrementing = false;   // <--- wajib
    protected $keyType = 'string';  // <--- wajib

    /** Status keahlian: LITE | ACTIVE | GRADUATE | HYPE */
    public const STATUS_KEAHLIAN_LITE = 'LITE';
    public const STATUS_KEAHLIAN_ACTIVE = 'ACTIVE';
    public const STATUS_KEAHLIAN_GRADUATE = 'GRADUATE';
    public const STATUS_KEAHLIAN_HYPE = 'HYPE';

    protected $fillable = [
        'name',
        'nickname',
        'public_id',
        'status_keahlian',
        'affiliate_pro_approved_at',
        'is_external_affiliate',
        'lite_education_seen',
        'phone',
        'email',
        'password',
        'country',
        'state',          // kalau kau simpan
        'district',       // kalau kau simpan
        'gender',
        'marital_status',
        'path',
        'poligami_situation',
        'poligami_level',
        'date_of_birth',
        'role',
        'photo_1',
        'photo_2',
        'photo_3',
        'photo_3_hobby_tag',
        'photo_4',
        'occupation_type',
        'government_level',
        'government_uniform',
        'government_role_level',
        'private_role',
        'business_scale',
        'hobbies',
        'social_activities',
        'education_level',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'   => 'datetime',
        'date_of_birth'       => 'date',
        'id'                  => 'string',
        'lite_education_seen' => 'boolean',
        'affiliate_pro_approved_at' => 'datetime',
        'is_external_affiliate' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!empty($user->public_id)) return;

            do {
                // pendek: 8 char uppercase
                $code = strtoupper(Str::random(8));
                // atau nak style: $code = 'JM' . strtoupper(Str::random(6));
            } while (static::where('public_id', $code)->exists());

            $user->public_id = $code;
        });
    }


    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }


    public function conversationsAsUserOne()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'user_one_id');
    }

    public function conversationsAsUserTwo()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'user_two_id');
    }

    public function conversations()
    {
        return \App\Models\Conversation::where(function ($q) {
            $q->where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id);
        });
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    /** Ahli LITE: belum subscribe / percuma */
    public function isLite(): bool
    {
        return ($this->status_keahlian ?? self::STATUS_KEAHLIAN_LITE) === self::STATUS_KEAHLIAN_LITE;
    }

    /** Ahli ACTIVE: sedang subscribe */
    public function isActive(): bool
    {
        return ($this->status_keahlian ?? '') === self::STATUS_KEAHLIAN_ACTIVE;
    }

    /** Ahli GRADUATE: tamat tempoh (pernah subscribe) */
    public function isGraduate(): bool
    {
        return ($this->status_keahlian ?? '') === self::STATUS_KEAHLIAN_GRADUATE;
    }

    /** Ahli HYPE: tamat tempoh & kemudian resubscribed */
    public function isHype(): bool
    {
        return ($this->status_keahlian ?? '') === self::STATUS_KEAHLIAN_HYPE;
    }

    /** Label untuk paparan */
    public function statusKeahlianLabel(): string
    {
        return match ($this->status_keahlian ?? self::STATUS_KEAHLIAN_LITE) {
            self::STATUS_KEAHLIAN_ACTIVE => 'Ahli Aktif',
            self::STATUS_KEAHLIAN_GRADUATE => 'Ahli Graduate',
            self::STATUS_KEAHLIAN_HYPE => 'Ahli Hype',
            default => 'Ahli Lite',
        };
    }

    /**
     * ID calon (user) yang saya telah "sapa" dahulu (first message from me), susunan ikut masa sapaan pertama.
     * Digunakan untuk had gambar LITE: hanya 10 pertama dapat lihat gambar jelas.
     *
     * @return array<int, string> array of other user IDs (max 10)
     */
    public function getFirstTenGreetedUserIds(): array
    {
        $myId = $this->id;
        $convIds = \App\Models\Conversation::query()
            ->where(function ($q) use ($myId) {
                $q->where('user_one_id', $myId)->orWhere('user_two_id', $myId);
            })
            ->pluck('id')
            ->all();

        if (empty($convIds)) {
            return [];
        }

        $firstMessageAt = \App\Models\Message::query()
            ->whereIn('conversation_id', $convIds)
            ->where('sender_id', $myId)
            ->selectRaw('conversation_id, MIN(created_at) as first_at')
            ->groupBy('conversation_id')
            ->orderByRaw('MIN(created_at) ASC')
            ->limit(10)
            ->pluck('first_at', 'conversation_id')
            ->all();

        $orderedConvIds = array_keys($firstMessageAt);
        if (empty($orderedConvIds)) {
            return [];
        }

        $convs = \App\Models\Conversation::query()
            ->whereIn('id', $orderedConvIds)
            ->get(['id', 'user_one_id', 'user_two_id']);

        $result = [];
        foreach ($orderedConvIds as $cid) {
            $c = $convs->firstWhere('id', $cid);
            if (!$c) continue;
            $otherId = ($c->user_one_id === $myId) ? $c->user_two_id : $c->user_one_id;
            $result[] = $otherId;
        }

        return array_values($result);
    }

    /**
     * Segerakkan status_keahlian dengan subscription: jika tiada langganan aktif dan status ACTIVE/HYPE, set GRADUATE.
     */
    public function syncStatusKeahlianFromSubscription(): void
    {
        $status = $this->status_keahlian ?? self::STATUS_KEAHLIAN_LITE;
        if ($status !== self::STATUS_KEAHLIAN_ACTIVE && $status !== self::STATUS_KEAHLIAN_HYPE) {
            return;
        }
        $hasActive = \App\Models\Subscription::where('user_id', $this->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->exists();
        if (!$hasActive) {
            $this->update(['status_keahlian' => self::STATUS_KEAHLIAN_GRADUATE]);
        }
    }
}
