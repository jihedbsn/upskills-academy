<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Product;
use App\Models\Book;
use App\Models\Order;
use App\Models\ResultatQuiz;
use App\Models\Discussion;
use App\Models\Category;
use App\Models\Teacher;
use App\Models\Quiz;
use App\Models\Ticket;
use App\Models\ResponseTicket;
use Illuminate\Support\Facades\Hash;

class StudentDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des catégories
        $categories = [
            ['titre_en' => 'Programming', 'titre_ar' => 'البرمجة', 'titre' => 'Programming'],
            ['titre_en' => 'Web Development', 'titre_ar' => 'تطوير الويب', 'titre' => 'Web Development'],
            ['titre_en' => 'Data Science', 'titre_ar' => 'علم البيانات', 'titre' => 'Data Science'],
            ['titre_en' => 'Design', 'titre_ar' => 'التصميم', 'titre' => 'Design'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['titre_en' => $cat['titre_en']], $cat);
        }

        // Créer des enseignants
        $teachers = [
            ['nom' => 'Dr. Ahmed', 'prenom' => 'Hassan', 'email' => 'ahmed@academy.com', 'password' => Hash::make('password')],
            ['nom' => 'Prof. Sarah', 'prenom' => 'Johnson', 'email' => 'sarah@academy.com', 'password' => Hash::make('password')],
            ['nom' => 'Dr. Mohammed', 'prenom' => 'Ali', 'email' => 'mohammed@academy.com', 'password' => Hash::make('password')],
        ];

        foreach ($teachers as $teacher) {
            Teacher::firstOrCreate(['email' => $teacher['email']], $teacher);
        }

        // Créer des cours
        $courses = [
            [
                'video' => 'python-basics.mp4',
                'prix' => 99.99,
                'categories_id' => Category::where('titre_en', 'Programming')->first()->id,
                'teacher_id' => Teacher::first()->id,
                'statut' => 1,
                'langue' => 'en',
                'online' => true,
                'classroom' => false
            ],
            [
                'video' => 'laravel-web-dev.mp4',
                'prix' => 149.99,
                'categories_id' => Category::where('titre_en', 'Web Development')->first()->id,
                'teacher_id' => Teacher::first()->id,
                'statut' => 1,
                'langue' => 'en',
                'online' => true,
                'classroom' => false
            ],
            [
                'video' => 'data-science-fundamentals.mp4',
                'prix' => 199.99,
                'categories_id' => Category::where('titre_en', 'Data Science')->first()->id,
                'teacher_id' => Teacher::skip(1)->first()->id,
                'statut' => 1,
                'langue' => 'en',
                'online' => true,
                'classroom' => false
            ],
        ];

        foreach ($courses as $course) {
            Product::firstOrCreate(['video' => $course['video']], $course);
        }

        // Créer des livres
        $books = [
            [
                'titre_en' => 'Advanced Python Programming',
                'titre_ar' => 'برمجة بايثون المتقدمة',
                'description_short_en' => 'Advanced Python concepts and techniques',
                'description_short_ar' => 'مفاهيم وتقنيات بايثون المتقدمة',
                'description_en' => 'Comprehensive guide to advanced Python programming',
                'description_ar' => 'دليل شامل لبرمجة بايثون المتقدمة',
                'prix' => 29.99,
                'image' => 'python-book.jpg'
            ],
            [
                'titre_en' => 'Laravel Best Practices',
                'titre_ar' => 'أفضل ممارسات لارافيل',
                'description_short_en' => 'Best practices for Laravel development',
                'description_short_ar' => 'أفضل الممارسات لتطوير لارافيل',
                'description_en' => 'Complete guide to Laravel best practices',
                'description_ar' => 'دليل شامل لأفضل ممارسات لارافيل',
                'prix' => 39.99,
                'image' => 'laravel-book.jpg'
            ],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(['titre_en' => $book['titre_en']], $book);
        }

        // Créer un étudiant de test
        $student = Student::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => 'student@test.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'country' => 'United States',
                'image' => 'default-avatar.jpg'
            ]
        );

        // Créer des quiz pour les cours
        $courses = Product::all();
        foreach ($courses as $course) {
            $quiz = Quiz::firstOrCreate(
                ['name_en' => 'Course ' . $course->id . ' Quiz'],
                [
                    'name_en' => 'Course ' . $course->id . ' Quiz',
                    'name_ar' => 'اختبار الدورة ' . $course->id,
                    'score' => 100,
                    'type_id' => 1
                ]
            );

            // Associer le quiz au cours
            $course->quizzes()->syncWithoutDetaching([$quiz->id]);
        }

        // Créer des accès aux cours pour l'étudiant
        foreach ($courses as $course) {
            $student->productAccess()->firstOrCreate(
                ['product_id' => $course->id],
                [
                    'product_id' => $course->id,
                    'student_id' => $student->id,
                    'date' => now(),
                    'is_active' => true,
                    'access_granted_at' => now()
                ]
            );
        }

        // Créer des commandes
        $orders = [
            [
                'product_id' => $courses->first()->id,
                'book_id' => null,
                'student_id' => $student->id,
                'price' => 99.99,
                'payment_success' => true,
                'payment_method' => 'credit_card',
                'transaction_id' => 'TXN_' . uniqid(),
                'notes' => 'Course purchase'
            ],
            [
                'product_id' => null,
                'book_id' => Book::first()->id,
                'student_id' => $student->id,
                'price' => 29.99,
                'payment_success' => true,
                'payment_method' => 'paypal',
                'transaction_id' => 'TXN_' . uniqid(),
                'notes' => 'Book purchase'
            ],
        ];

        foreach ($orders as $order) {
            Order::firstOrCreate(
                ['transaction_id' => $order['transaction_id']],
                $order
            );
        }

        // Créer des résultats de quiz
        $quizResults = [
            [
                'student_id' => $student->id,
                'product_id' => $courses->first()->id,
                'quiz_id' => Quiz::first()->id,
                'score' => 85.5,
                'success' => true,
                'attempts' => 1,
                'started_at' => now()->subDays(5),
                'completed_at' => now()->subDays(5)->addMinutes(25)
            ],
            [
                'student_id' => $student->id,
                'product_id' => $courses->skip(1)->first()->id,
                'quiz_id' => Quiz::skip(1)->first()->id,
                'score' => 92.0,
                'success' => true,
                'attempts' => 1,
                'started_at' => now()->subDays(3),
                'completed_at' => now()->subDays(3)->addMinutes(20)
            ],
        ];

        foreach ($quizResults as $result) {
            ResultatQuiz::firstOrCreate(
                [
                    'student_id' => $result['student_id'],
                    'product_id' => $result['product_id'],
                    'quiz_id' => $result['quiz_id']
                ],
                $result
            );
        }

        // Créer des tickets de support
        $tickets = [
            [
                'student_id' => $student->id,
                'sujet' => 'Problème d\'accès au contenu vidéo',
                'ticket_iscomplet' => false
            ],
            [
                'student_id' => $student->id,
                'sujet' => 'Demande de certificat',
                'ticket_iscomplet' => true
            ],
            [
                'student_id' => $student->id,
                'sujet' => 'Question sur le cours Python',
                'ticket_iscomplet' => false
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::firstOrCreate(
                [
                    'student_id' => $ticketData['student_id'],
                    'sujet' => $ticketData['sujet']
                ],
                $ticketData
            );

            // Créer la première réponse de l'étudiant
            ResponseTicket::firstOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'student_id' => $ticket->student_id,
                    'isAdmin' => false
                ],
                [
                    'ticket_id' => $ticket->id,
                    'student_id' => $ticket->student_id,
                    'message' => $this->getStudentMessage($ticket->sujet),
                    'isAdmin' => false
                ]
            );

            // Créer une réponse d'admin pour certains tickets
            if ($ticket->sujet === 'Demande de certificat') {
                ResponseTicket::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'isAdmin' => true
                    ],
                    [
                        'ticket_id' => $ticket->id,
                        'student_id' => null, // Admin n'a pas de student_id
                        'message' => 'Merci pour votre demande. Votre certificat sera disponible dans votre tableau de bord dans les 24-48 heures.',
                        'isAdmin' => true
                    ]
                );

                // Réponse de l'étudiant
                ResponseTicket::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'student_id' => $ticket->student_id,
                        'isAdmin' => false,
                        'message' => 'Parfait, merci beaucoup !'
                    ],
                    [
                        'ticket_id' => $ticket->id,
                        'student_id' => $ticket->student_id,
                        'message' => 'Parfait, merci beaucoup !',
                        'isAdmin' => false
                    ]
                );
            } elseif ($ticket->sujet === 'Question sur le cours Python') {
                ResponseTicket::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'isAdmin' => true
                    ],
                    [
                        'ticket_id' => $ticket->id,
                        'student_id' => null,
                        'message' => 'Bonjour ! Pouvez-vous me donner plus de détails sur votre question concernant le cours Python ?',
                        'isAdmin' => true
                    ]
                );
            }
        }

        $this->command->info('Student Dashboard test data created successfully!');
        $this->command->info('Test student email: student@test.com');
        $this->command->info('Test student password: password');
    }

    /**
     * Générer un message d'étudiant basé sur le sujet du ticket
     */
    private function getStudentMessage($sujet)
    {
        $messages = [
            'Problème d\'accès au contenu vidéo' => 'Bonjour, je n\'arrive pas à accéder aux vidéos du cours. Pouvez-vous m\'aider ?',
            'Demande de certificat' => 'J\'ai terminé le cours et j\'aimerais recevoir mon certificat. Comment procéder ?',
            'Question sur le cours Python' => 'J\'ai une question sur le chapitre 3 du cours Python. Pouvez-vous m\'expliquer ?'
        ];

        return $messages[$sujet] ?? 'J\'ai besoin d\'aide avec ce ticket.';
    }
}
