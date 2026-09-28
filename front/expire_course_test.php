<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\ProductStudent;
use App\Models\Product;

echo "Testing Course Expiration Blocking\n";
echo "====================================\n\n";

$student = Student::where('email', 'ahmad@gmail.com')->first();
if (!$student) {
    echo "❌ Student with email ahmad@gmail.com not found.\n";
    exit(1);
}

echo "✅ Found student: {$student->full_name} (ID: {$student->id})\n\n";

$enrollment = ProductStudent::where('student_id', $student->id)
    ->where('product_id', 52)
    ->first();

if (!$enrollment) {
    echo "❌ Student is not enrolled in course 52.\n";
    exit(1);
}

$product = Product::find(52);
echo "Course: {$product->titre}\n\n";

echo "BEFORE:\n";
echo "  - is_active: " . ($enrollment->is_active ? 'YES' : 'NO') . "\n";
echo "  - is_expired: " . ($enrollment->is_expired ? 'YES' : 'NO') . "\n";
echo "  - expiration_date: {$enrollment->expiration_date}\n";
echo "  - hasAccessToProduct: " . ($student->hasAccessToProduct(52) ? 'YES' : 'NO') . "\n\n";

$enrollment->update(['is_expired' => true]);
$enrollment->refresh();

echo "AFTER marking as expired:\n";
echo "  - is_active: " . ($enrollment->is_active ? 'YES' : 'NO') . "\n";
echo "  - is_expired: " . ($enrollment->is_expired ? 'YES' : 'NO') . "\n";
echo "  - expiration_date: {$enrollment->expiration_date}\n";
echo "  - hasAccessToProduct: " . ($student->hasAccessToProduct(52) ? 'YES' : 'NO') . "\n\n";

echo "✅ Course 52 has been marked as EXPIRED for ahmad@gmail.com\n";
echo "🧪 You can now test by:\n";
echo "   1. Login as ahmad@gmail.com\n";
echo "   2. Try to access course 52\n";
echo "   3. You should see 'Course Expired' message in red\n";
echo "   4. Quiz/exam buttons should be disabled\n\n";

echo "To restore access, run:\n";
echo "UPDATE product_students SET is_expired = 0 WHERE student_id = {$student->id} AND product_id = 52;\n";
