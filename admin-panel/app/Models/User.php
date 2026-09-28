<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nom',
        'email',
        'telephone',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    // Relations pour le système de gestion des cours
    public function purchasedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_students', 'student_id', 'product_id')
                    ->withPivot('date', 'is_active', 'access_granted_at')
                    ->withTimestamps();
    }

    public function productStudents()
    {
        return $this->hasMany(ProductStudent::class, 'student_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'student_id');
    }

    public function resultatQuizzes()
    {
        return $this->hasMany(ResultatQuiz::class, 'student_id');
    }

    public function responseQuizzes()
    {
        return $this->hasMany(ResponseQuiz::class, 'student_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'student_id');
    }

    public function courseRatings()
    {
        return $this->hasMany(CourseRating::class, 'student_id');
    }

    public function adminResponses()
    {
        return $this->hasMany(ResponseDiscussion::class, 'admin_id');
    }

    // Méthodes utilitaires
    public function hasPurchasedProduct($productId)
    {
        return $this->productStudents()
                    ->where('product_id', $productId)
                    ->where('is_active', true)
                    ->exists();
    }

    public function getProductAccess($productId)
    {
        return $this->productStudents()
                    ->where('product_id', $productId)
                    ->first();
    }

    public function getQuizResult($productId, $quizId)
    {
        return $this->resultatQuizzes()
                    ->where('product_id', $productId)
                    ->where('quiz_id', $quizId)
                    ->first();
    }

    public function canRateProduct($productId)
    {
        // L'étudiant peut noter s'il a réussi le cours
        $result = $this->resultatQuizzes()
                       ->where('product_id', $productId)
                       ->where('success', true)
                       ->exists();

        // Et s'il n'a pas déjà noté
        $hasRated = $this->courseRatings()
                         ->where('product_id', $productId)
                         ->exists();

        return $result && !$hasRated;
    }

    public function isAdmin()
    {
        // Logique pour déterminer si l'utilisateur est admin
        // Vérifier plusieurs champs possibles selon votre structure
        return $this->role === 'admin' ||
               $this->is_admin === true ||
               $this->type === 'admin' ||
               $this->user_type === 'admin';
    }

    /**
     * Automatically determine customer type based on purchase history
     *
     * @return string 'new', 'returning', or 'vip'
     */
    public function getCustomerType(): string
    {
        $successfulOrdersCount = $this->orders()
            ->where('payment_success', true)
            ->count();

        // Calculate total spent amount for VIP determination
        $totalSpent = $this->orders()
            ->where('payment_success', true)
            ->sum('total_amount');

        // Define thresholds
        if ($successfulOrdersCount == 0) {
            return 'new';
        }

        // VIP criteria: 5+ orders OR spent more than $1000
        if ($successfulOrdersCount >= 5 || $totalSpent >= 1000) {
            return 'vip';
        }

        // Returning customers: 1-4 successful orders
        return 'returning';
    }

    /**
     * Check if user is a new customer (no successful purchases)
     */
    public function isNewCustomer(): bool
    {
        return $this->getCustomerType() === 'new';
    }

    /**
     * Check if user is a returning customer
     */
    public function isReturningCustomer(): bool
    {
        return $this->getCustomerType() === 'returning';
    }

    /**
     * Check if user is a VIP customer
     */
    public function isVipCustomer(): bool
    {
        return $this->getCustomerType() === 'vip';
    }

    /**
     * Get customer statistics for admin display
     */
    public function getCustomerStats(): array
    {
        $successfulOrders = $this->orders()->where('payment_success', true);

        return [
            'customer_type' => $this->getCustomerType(),
            'total_orders' => $successfulOrders->count(),
            'total_spent' => $successfulOrders->sum('total_amount'),
            'first_order_date' => $successfulOrders->orderBy('created_at')->first()?->created_at,
            'last_order_date' => $successfulOrders->orderBy('created_at', 'desc')->first()?->created_at,
        ];
    }

    /**
     * Vérifier si l'utilisateur a complété un cours
     */
    public function hasCompletedProduct($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        return $product->hasStudentCompleted($this->id);
    }

    /**
     * Obtenir le pourcentage de progression pour un cours
     */
    public function getProductProgress($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        return $product->getStudentProgress($this->id);
    }

    /**
     * Obtenir tous les cours complétés
     */
    public function getCompletedProducts()
    {
        $completedProducts = collect();

        foreach ($this->purchasedProducts as $product) {
            if ($product->hasStudentCompleted($this->id)) {
                $completedProducts->push($product);
            }
        }

        return $completedProducts;
    }

    /**
     * Obtenir tous les cours en cours
     */
    public function getInProgressProducts()
    {
        $inProgressProducts = collect();

        foreach ($this->purchasedProducts as $product) {
            if (!$product->hasStudentCompleted($this->id)) {
                $inProgressProducts->push($product);
            }
        }

        return $inProgressProducts;
    }

    /**
     * Obtenir les statistiques de l'étudiant
     */
    public function getStudentStatistics()
    {
        $totalPurchased = $this->purchasedProducts()->count();
        $completedProducts = $this->getCompletedProducts()->count();
        $inProgressProducts = $this->getInProgressProducts()->count();

        $successRate = $totalPurchased > 0 ? round(($completedProducts / $totalPurchased) * 100, 1) : 0;

        return [
            'total_purchased' => $totalPurchased,
            'completed_products' => $completedProducts,
            'in_progress_products' => $inProgressProducts,
            'success_rate' => $successRate
        ];
    }

    /**
     * Vérifier si l'utilisateur peut passer un examen
     */
    public function canTakeExam($productId, $examId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        return $product->canStudentTakeExam($this->id, $examId);
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return $this->name;
    }

    /**
     * Obtenir les initiales de l'utilisateur
     */
    public function getInitialsAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }
}
