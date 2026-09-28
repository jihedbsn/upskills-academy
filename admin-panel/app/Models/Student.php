<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'country',
        'image',
        'email_verified_at',
        'birthdate',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'referred_by_code',
        'referral_reward_preference',
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
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
        ];
    }

    /**
     * Get the student's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the student's image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return config('app.file_url') . 'uploads/students/' . $this->image;
        }
        return asset('assets/img/students/default-avatar.png');
    }

    /**
     * Get the student's product access records.
     */
    public function productAccess(): HasMany
    {
        return $this->hasMany(ProductStudent::class);
    }

    /**
     * Get the student's installment orders.
     */
    public function installmentOrders(): HasMany
    {
        return $this->hasMany(OrderSpecifique::class);
    }

    /**
     * Check if the student has any suspended installment orders.
     */
    public function hasSuspendedOrders(): bool
    {
        return $this->installmentOrders()
            ->where('is_suspended', true)
            ->exists();
    }

    /**
     * Get the student's quiz results.
     */
    public function quizResults(): HasMany
    {
        return $this->hasMany(ResultatQuiz::class);
    }

    /**
     * Get the student's quiz responses.
     */
    public function quizResponses(): HasMany
    {
        return $this->hasMany(ResponseQuiz::class);
    }

    /**
     * Get the student's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the student's cart items.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Check if the student has access to a specific product.
     */
    public function hasAccessToProduct($productId): bool
    {
        return $this->productAccess()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if the student has purchased a specific product.
     */
    public function hasPurchasedProduct($productId): bool
    {
        return $this->hasAccessToProduct($productId);
    }

    /**
     * Get the student's quiz result for a specific quiz.
     */
    public function getQuizResult($productId, $quizId)
    {
        return $this->quizResults()
            ->where('product_id', $productId)
            ->where('quiz_id', $quizId)
            ->first();
    }

    /**
     * Check if the student has completed a specific quiz.
     */
    public function hasCompletedQuiz($productId, $quizId): bool
    {
        $result = $this->getQuizResult($productId, $quizId);
        return $result && $result->success;
    }

    /**
     * Check if the student is blocked from a specific quiz.
     */
    public function isBlockedFromQuiz($productId, $quizId): bool
    {
        $result = $this->getQuizResult($productId, $quizId);
        return $result && $result->attempts >= 3 && !$result->success;
    }

    /**
     * Get the student's progress for a specific product.
     */
    public function getProductProgress($productId): array
    {
        $product = Product::find($productId);
        if (!$product) {
            return ['total_quizzes' => 0, 'completed_quizzes' => 0, 'progress_percentage' => 0];
        }

        $totalQuizzes = $product->quizzes->count();
        $completedQuizzes = $product->quizzes->filter(function($quiz) use ($productId) {
            return $this->hasCompletedQuiz($productId, $quiz->id);
        })->count();

        $progressPercentage = $totalQuizzes > 0 ? ($completedQuizzes / $totalQuizzes) * 100 : 0;

        return [
            'total_quizzes' => $totalQuizzes,
            'completed_quizzes' => $completedQuizzes,
            'progress_percentage' => round($progressPercentage, 1)
        ];
    }

    /**
     * Check if the student has completed a specific product.
     */
    public function hasCompletedProduct($productId): bool
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $totalQuizzes = $product->quizzes->count();
        if ($totalQuizzes === 0) {
            return true; // Si pas de quiz, considérer comme complété
        }

        $completedQuizzes = $product->quizzes->filter(function($quiz) use ($productId) {
            return $this->hasCompletedQuiz($productId, $quiz->id);
        })->count();

        return $completedQuizzes === $totalQuizzes;
    }

    /**
     * Check if the student can rate a specific product.
     */
    public function canRateProduct($productId): bool
    {
        // L'étudiant doit avoir acheté ET complété le cours
        return $this->hasPurchasedProduct($productId) && $this->hasCompletedProduct($productId);
    }

    /**
     * Check if the student has already rated a specific product.
     */
    public function hasRatedProduct($productId): bool
    {
        return $this->courseRatings()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get the student's course ratings.
     */
    public function courseRatings(): HasMany
    {
        return $this->hasMany(CourseRating::class);
    }

    /**
     * Get the student's discussions.
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Get the student's admin responses to discussions.
     */
    public function adminResponses(): HasMany
    {
        return $this->hasMany(ResponseDiscussion::class, 'admin_id');
    }

    /**
     * Check if the student is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_admin === true;
    }

    /**
     * Get the student's initials for avatar display.
     */
    public function getInitialsAttribute(): string
    {
        $first = substr($this->first_name, 0, 1) ?? '';
        $last = substr($this->last_name, 0, 1) ?? '';
        return strtoupper($first . $last);
    }

    public function referralCode(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReferralCode::class, 'user_id');
    }

    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referralReceived(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    public function referralRewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class, 'user_id');
    }
}
