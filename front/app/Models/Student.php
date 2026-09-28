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
        'address',
        'city',
        'zip_code',
        'gender',
        'birthdate',
        'image',
        'gravatar_email',
        'email_verified_at',
        'facebook_id',
        'google_id',
        'avatar',
        'last_evaluation_reminder',
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
            'birthdate' => 'date',
            'password' => 'hashed',
            'last_evaluation_reminder' => 'datetime',
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
        $fullName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        // If no name (empty or just whitespace), return email username part
        if (empty($fullName)) {
            return explode('@', $this->email)[0] ?? 'Student';
        }

        return $fullName;
    }

    /**
     * Get the student's image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        // Si l'utilisateur a un Gravatar email, utiliser Gravatar
        if ($this->gravatar_email) {
            $hash = md5(strtolower(trim($this->gravatar_email)));
            return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=200";
        }

        // Sinon, utiliser l'image uploadée
        if ($this->image) {
            // Vérifier d'abord dans le nouveau répertoire
            $newPath = public_path('uploads/students/' . $this->image);
            if (file_exists($newPath)) {
                return asset('uploads/students/' . $this->image);
            }

            // Vérifier dans l'ancien répertoire storage
            $oldPath = storage_path('app/public/uploads/students/' . $this->image);
            if (file_exists($oldPath)) {
                return asset('storage/uploads/students/' . $this->image);
            }

            // Si l'image existe dans le nouveau répertoire mais pas trouvée, utiliser le nouveau chemin
            return asset('uploads/students/' . $this->image);
        }

        // Image par défaut
        return asset('assets/img/students/default-avatar.png');
    }

    /**
     * Indique si l'utilisateur a une image (upload) ou un Gravatar configuré.
     */
    public function getHasAvatarAttribute(): bool
    {
        return (bool) ($this->gravatar_email || $this->image);
    }

    public function historiqueQuizzes()
    {
        return $this->hasMany(HistoriqueQuiz::class, 'student_id');
    }

    public function resultatQuizzes()
    {
        return $this->hasMany(ResultatQuiz::class, 'student_id');
    }

    public function responseQuizzes()
    {
        return $this->hasMany(ResponseQuiz::class, 'student_id');
    }

    public function productStudents()
    {
        return $this->hasMany(ProductStudent::class, 'student_id');
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
     * Get suspended installment orders.
     */
    public function suspendedOrders()
    {
        return $this->installmentOrders()
            ->where('is_suspended', true)
            ->with('product')
            ->get();
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
            ->where('is_expired', false)
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
        // L'étudiant doit avoir acheté le cours
        return $this->hasPurchasedProduct($productId);
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
     * Get the student's tickets.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the student's ticket responses.
     */
    public function ticketResponses(): HasMany
    {
        return $this->hasMany(ResponseTicket::class);
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
        // If first/last name exist, use them
        if ($this->first_name || $this->last_name) {
            $first = substr($this->first_name ?? '', 0, 1);
            $last = substr($this->last_name ?? '', 0, 1);
            return strtoupper($first . $last);
        }

        // Otherwise use first 2 chars of email
        $emailPart = explode('@', $this->email)[0] ?? 'ST';
        return strtoupper(substr($emailPart, 0, 2));
    }

    /**
     * Get customer type based on order history
     * Returns: 'new', 'returning', or 'vip'
     */
    public function getCustomerType(): string
    {
        $orderCount = $this->orders()->where('payment_success', true)->count();
        $totalSpent = $this->orders()->where('payment_success', true)->sum('price');

        if ($orderCount === 0) {
            return 'new';
        }

        // VIP: 5+ orders or spent $1000+
        if ($orderCount >= 5 || $totalSpent >= 1000) {
            return 'vip';
        }

        // Returning: 1-4 orders
        return 'returning';
    }

    /**
     * Check if student is a new customer (no successful orders)
     */
    public function isNewCustomer(): bool
    {
        return $this->orders()->where('payment_success', true)->count() === 0;
    }

    /**
     * Check if student is a returning customer (1-4 orders)
     */
    public function isReturningCustomer(): bool
    {
        $orderCount = $this->orders()->where('payment_success', true)->count();
        return $orderCount >= 1 && $orderCount <= 4;
    }

    /**
     * Check if student is a VIP customer (5+ orders or $1000+ spent)
     */
    public function isVipCustomer(): bool
    {
        $orderCount = $this->orders()->where('payment_success', true)->count();
        $totalSpent = $this->orders()->where('payment_success', true)->sum('price');

        return $orderCount >= 5 || $totalSpent >= 1000;
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats(): array
    {
        return [
            'total_orders' => $this->orders()->where('payment_success', true)->count(),
            'total_spent' => $this->orders()->where('payment_success', true)->sum('price'),
            'customer_type' => $this->getCustomerType()
        ];
    }

    /**
     * Get coupon usage logs for this student
     */
    public function couponUsageLogs()
    {
        return $this->hasMany(CouponUsageLog::class, 'user_id');
    }
}
