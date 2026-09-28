<?php

namespace App\Services;

use App\Helpers\PasswordGenerator;
use App\Jobs\SendStudentEnrollmentEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudentService
{
    public function index()
    {
        try {
            $products = Product::with(['variations' => function ($query) {
                $query->where('langue', 'ar');
            }])->get();

            $products->each(function ($product) {
                if ($product->variations->isEmpty()) {
                    $product->setAttribute('titre', 'Cours #' . $product->id);
                }
            });

            return view('import-students.index', compact('products'));
        } catch (\Exception $e) {
            Log::error('Erreur dans ImportStudentController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $products = collect();
            return view('import-students.index', compact('products'));
        }
    }

    public function import(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ], [
                'product_id.required' => 'Veuillez sélectionner un cours.',
                'product_id.exists' => 'Le cours sélectionné n\'existe pas.',
                'excel_file.required' => 'Veuillez sélectionner un fichier Excel.',
                'excel_file.file' => 'Le fichier sélectionné n\'est pas valide.',
                'excel_file.mimes' => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV.',
                'excel_file.max' => 'Le fichier ne doit pas dépasser 10MB.'
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreurs de validation',
                        'errors' => $validator->errors()->toArray()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $productId = $request->product_id;
            $file = $request->file('excel_file');

            $product = Product::findOrFail($productId);
            $productPrice = $product->prix ?? 0;

            $data = Excel::toArray(new class implements ToModel, WithHeadingRow {
                public function model(array $row)
                {
                    return $row;
                }
            }, $file);

            if (empty($data) || empty($data[0])) {
                throw new \Exception('Le fichier Excel est vide ou ne contient pas de données valides. Veuillez vérifier que le fichier contient des données et que la première ligne contient les en-têtes de colonnes.');
            }

            $rows = $data[0];
            $importedCount = 0;
            $skippedCount = 0;
            $alreadyExistsCount = 0;
            $errors = [];
            $emailSequence = 0;
            $emailStaggerSeconds = 2;
            $accountBaseDelaySeconds = 1;
            $enrollmentBaseDelaySeconds = 12;

            foreach ($rows as $rowNumber => $row) {
                $rowNumber++;

                try {
                    $firstName = trim($row['first_name'] ?? $row['FIRST_NAME'] ?? $row['first name'] ?? '');
                    $lastName = trim($row['last_name'] ?? $row['LAST_NAME'] ?? $row['last name'] ?? '');

                    if (empty($firstName) && empty($lastName)) {
                        $fullName = trim($row['full_name_en'] ?? $row['FULL_NAME_EN'] ?? $row['full_name'] ?? $row['FULL_NAME'] ?? '');
                        if (!empty($fullName)) {
                            $nameParts = explode(' ', $fullName, 2);
                            $firstName = $nameParts[0] ?? '';
                            $lastName = $nameParts[1] ?? $nameParts[0];
                        }
                    }

                    $email = trim($row['email'] ?? $row['EMAIL'] ?? $row['agent_email'] ?? $row['AGENT_EMAIL'] ?? $row['username'] ?? $row['USERNAME'] ?? '');
                    $phone = trim($row['phone'] ?? $row['PHONE'] ?? $row['mobile'] ?? $row['MOBILE'] ?? $row['telephone'] ?? $row['TELEPHONE'] ?? '');

                    $lineErrors = [];
                    if (empty($firstName)) {
                        $lineErrors[] = 'Prénom manquant';
                    }
                    if (empty($lastName)) {
                        $lineErrors[] = 'Nom manquant';
                    }
                    if (empty($email)) {
                        $lineErrors[] = 'Email manquant';
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $lineErrors[] = 'Format email invalide';
                    }
                    if (empty($phone)) {
                        $lineErrors[] = 'Téléphone manquant';
                    }

                    if (!empty($lineErrors)) {
                        $errors[] = "Ligne {$rowNumber}: " . implode(', ', $lineErrors) . '.';
                        $skippedCount++;
                        continue;
                    }

                    $student = Student::where('email', $email)->first();
                    $isNewStudent = false;
                    $randomPassword = null;

                    if (!$student) {
                        $randomPassword = PasswordGenerator::generateSimple(10);

                        $student = Student::create([
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $email,
                            'phone' => $phone,
                            'password' => Hash::make($randomPassword),
                            'email_verified_at' => now(),
                        ]);

                        $isNewStudent = true;
                    }

                    $existingOrder = Order::where('student_id', $student->id)
                        ->where('product_id', $productId)
                        ->where('payment_success', 1)
                        ->first();

                    if ($existingOrder) {
                        $existingProductStudent = ProductStudent::where('student_id', $student->id)
                            ->where('product_id', $productId)
                            ->first();

                        if (!$existingProductStudent) {
                            ProductStudent::create([
                                'student_id' => $student->id,
                                'product_id' => $productId,
                                'date' => now()->toDateString(),
                                'is_active' => true,
                                'access_granted_at' => now(),
                            ]);
                        } else {
                            if (!$existingProductStudent->is_active) {
                                $existingProductStudent->grantAccess();
                            }
                        }

                        $alreadyExistsCount++;
                        $importedCount++;
                        continue;
                    }

                    Order::create([
                        'student_id' => $student->id,
                        'product_id' => $productId,
                        'payment_success' => 1,
                        'payment_method' => 'cache',
                        'price' => $productPrice,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $existingProductStudent = ProductStudent::where('student_id', $student->id)
                        ->where('product_id', $productId)
                        ->first();

                    $isNewEnrollment = false;

                    if (!$existingProductStudent) {
                        ProductStudent::create([
                            'student_id' => $student->id,
                            'product_id' => $productId,
                            'date' => now()->toDateString(),
                            'is_active' => true,
                            'access_granted_at' => now(),
                        ]);
                        $isNewEnrollment = true;
                    } else {
                        if (!$existingProductStudent->is_active) {
                            $existingProductStudent->grantAccess();
                            $isNewEnrollment = true;
                        }
                    }

                    try {
                        $queuedAnyEmail = false;
                        $accountDelaySeconds = $accountBaseDelaySeconds + ($emailSequence * $emailStaggerSeconds);
                        $enrollmentDelaySeconds = $enrollmentBaseDelaySeconds + ($emailSequence * $emailStaggerSeconds);
                       
                        if ($isNewStudent && $randomPassword) {
                           
                            Log::info("Queuing account creation email for: {$email} with {$accountDelaySeconds}s delay");
                            SendStudentEnrollmentEmail::dispatch($student, $product, 'account_created', $randomPassword)
                                ->delay(now()->addSeconds($accountDelaySeconds))
                                ->afterCommit();
                            $queuedAnyEmail = true;
                        }

                        if ($isNewEnrollment) {
                        
                                 Log::info("Queuing course enrollment email for: {$email} with {$enrollmentDelaySeconds}s delay");
                            SendStudentEnrollmentEmail::dispatch($student, $product, 'course_enrollment')
                                ->delay(now()->addSeconds($enrollmentDelaySeconds))
                                ->afterCommit();
                            $queuedAnyEmail = true;
                        }

                        if ($queuedAnyEmail) {
                            $emailSequence++;
                        }
                    } catch (\Exception $emailError) {
                       
                        Log::error("Error queuing email for {$email}: " . $emailError->getMessage());
                    }

                    if (!$isNewStudent) {
                        $alreadyExistsCount++;
                    }
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne {$rowNumber}: Erreur - " . $e->getMessage();
                    $skippedCount++;
                }
            }

            DB::commit();

            $newStudentsCount = $importedCount - $alreadyExistsCount;
            $message = "Importation terminée ! {$importedCount} étudiant(s) traité(s) avec succès.";

            if ($newStudentsCount > 0) {
                $message .= " {$newStudentsCount} nouvel(le)(s) étudiant(s) créé(s).";
            }
            if ($alreadyExistsCount > 0) {
                $message .= " {$alreadyExistsCount} étudiant(s) déjà inscrit(s).";
            }
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} ligne(s) ignorée(s) à cause d'erreurs.";
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'imported_count' => $importedCount,
                    'new_students_count' => $newStudentsCount,
                    'already_exists_count' => $alreadyExistsCount,
                    'skipped_count' => $skippedCount,
                    'errors' => $errors,
                    'redirect' => route('import-students.index')
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            try {
                DB::rollback();
            } catch (\Exception $rollbackException) {
                Log::error('Erreur lors du rollback: ' . $rollbackException->getMessage());
            }

            Log::error('Erreur lors de l\'importation des étudiants: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $errorMessage = 'Erreur lors de l\'importation: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'import_error'
                ], 400);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function addManual(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ], [
                'product_id.required' => 'Veuillez sélectionner un cours.',
                'product_id.exists' => 'Le cours sélectionné n\'existe pas.',
                'first_name.required' => 'Le prénom est requis.',
                'last_name.required' => 'Le nom est requis.',
                'email.required' => 'L\'email est requis.',
                'email.email' => 'L\'email doit être valide.',
                'phone.required' => 'Le téléphone est requis.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            DB::beginTransaction();

            $productId = $request->product_id;
            $firstName = trim($request->first_name);
            $lastName = trim($request->last_name);
            $email = trim($request->email);
            $phone = trim($request->phone);

            $product = Product::findOrFail($productId);
            $productPrice = $product->prix ?? 0;

            $student = Student::where('email', $email)->first();
            $isNewStudent = false;
            $randomPassword = null;

            if (!$student) {
                $randomPassword = PasswordGenerator::generateSimple(10);

                $student = Student::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make($randomPassword),
                    'email_verified_at' => now(),
                ]);

                $isNewStudent = true;
            }

            $existingOrder = Order::where('student_id', $student->id)
                ->where('product_id', $productId)
                ->where('payment_success', 1)
                ->first();

            if ($existingOrder) {
                $existingProductStudent = ProductStudent::where('student_id', $student->id)
                    ->where('product_id', $productId)
                    ->first();

                if (!$existingProductStudent) {
                    ProductStudent::create([
                        'student_id' => $student->id,
                        'product_id' => $productId,
                        'date' => now()->toDateString(),
                        'is_active' => true,
                        'access_granted_at' => now(),
                    ]);
                } else {
                    if (!$existingProductStudent->is_active) {
                        $existingProductStudent->grantAccess();
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'L\'étudiant est déjà inscrit à ce cours.',
                ]);
            }

            Order::create([
                'student_id' => $student->id,
                'product_id' => $productId,
                'payment_success' => 1,
                'payment_method' => 'cache',
                'price' => $productPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existingProductStudent = ProductStudent::where('student_id', $student->id)
                ->where('product_id', $productId)
                ->first();

            $isNewEnrollment = false;

            if (!$existingProductStudent) {
                ProductStudent::create([
                    'student_id' => $student->id,
                    'product_id' => $productId,
                    'date' => now()->toDateString(),
                    'is_active' => true,
                    'access_granted_at' => now(),
                ]);
                $isNewEnrollment = true;
            } else {
                if (!$existingProductStudent->is_active) {
                    $existingProductStudent->grantAccess();
                    $isNewEnrollment = true;
                }
            }

            try {
                $manualAccountDelaySeconds = 1;
                $manualEnrollmentDelaySeconds = 3;

                if ($isNewStudent && $randomPassword && $isNewEnrollment) {
                    Log::info("Queuing chained manual emails for: {$email} (account in {$manualAccountDelaySeconds}s, enrollment +{$manualEnrollmentDelaySeconds}s)");

                    $accountJob = (new SendStudentEnrollmentEmail($student, $product, 'account_created', $randomPassword))
                        ->delay(now()->addSeconds($manualAccountDelaySeconds))
                        ->afterCommit();

                    $enrollmentJob = (new SendStudentEnrollmentEmail($student, $product, 'course_enrollment'))
                        ->delay(now()->addSeconds($manualEnrollmentDelaySeconds))
                        ->afterCommit();

                    Bus::chain([$accountJob, $enrollmentJob])->dispatch();
                } else {
                    if ($isNewStudent && $randomPassword) {
                        Log::info("Queuing account creation email (manual) for: {$email} with {$manualAccountDelaySeconds}s delay");
                        SendStudentEnrollmentEmail::dispatch($student, $product, 'account_created', $randomPassword)
                            ->delay(now()->addSeconds($manualAccountDelaySeconds))
                            ->afterCommit();
                    }

                    if ($isNewEnrollment) {
                        Log::info("Queuing course enrollment email (manual) for: {$email} with {$manualEnrollmentDelaySeconds}s delay");
                        SendStudentEnrollmentEmail::dispatch($student, $product, 'course_enrollment')
                            ->delay(now()->addSeconds($manualEnrollmentDelaySeconds))
                            ->afterCommit();
                    }
                }
            } catch (\Exception $emailError) {
                Log::error("Error queuing email (manual) for {$email}: " . $emailError->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Étudiant ajouté avec succès au cours!',
            ]);
        } catch (\Exception $e) {
            try {
                DB::rollback();
            } catch (\Exception $rollbackException) {
                Log::error('Erreur lors du rollback: ' . $rollbackException->getMessage());
            }

            Log::error('Erreur lors de l\'ajout manuel de l\'étudiant: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage(),
            ], 400);
        }
    }
}
