<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatBotResponse;

class ChatBotResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $responses = [
            // Accueil et présentation
            [
                'keyword' => 'hello,hi,hey,bonjour,مرحبا',
                'question_en' => 'Hello! How can I help you today?',
                'question_ar' => 'مرحباً! كيف يمكنني مساعدتك اليوم؟',
                'response_en' => 'Hello! Welcome to Swedish Academy. I\'m here to help you with any questions about our courses, books, quizzes, and services. How can I assist you today?',
                'response_ar' => 'مرحباً! أهلاً وسهلاً بك في الأكاديمية السويدية. أنا هنا لمساعدتك في أي أسئلة حول دوراتنا وكتبنا واختباراتنا وخدماتنا. كيف يمكنني مساعدتك اليوم؟',
                'category' => 'greeting',
                'priority' => 10
            ],
            
            // À propos de l'académie
            [
                'keyword' => 'about,academy,swedish,académie,أكاديمية,سويدية',
                'question_en' => 'Tell me about Swedish Academy',
                'question_ar' => 'أخبرني عن الأكاديمية السويدية',
                'response_en' => 'Swedish Academy is a leading educational institution specializing in Swedish language courses, books, and cultural programs. We offer comprehensive learning experiences including interactive quizzes, study materials, and personalized support to help students master Swedish effectively.',
                'response_ar' => 'الأكاديمية السويدية هي مؤسسة تعليمية رائدة متخصصة في دورات اللغة السويدية والكتب والبرامج الثقافية. نقدم تجارب تعليمية شاملة تشمل اختبارات تفاعلية ومواد دراسية ودعم شخصي لمساعدة الطلاب على إتقان اللغة السويدية بفعالية.',
                'category' => 'about',
                'priority' => 9
            ],
            
            // Cours disponibles
            [
                'keyword' => 'courses,classes,duration,دورات,فصول,مدة',
                'question_en' => 'What courses do you offer?',
                'question_ar' => 'ما هي الدورات التي تقدمونها؟',
                'response_en' => 'We offer a variety of Swedish language courses including: Beginner Level (A1-A2), Intermediate Level (B1-B2), Advanced Level (C1-C2), Business Swedish, Swedish for Academic Purposes, and Cultural Immersion Programs. Each course includes interactive quizzes, study materials, and progress tracking.',
                'response_ar' => 'نقدم مجموعة متنوعة من دورات اللغة السويدية تشمل: المستوى المبتدئ (A1-A2)، المستوى المتوسط (B1-B2)، المستوى المتقدم (C1-C2)، السويدية للأعمال، السويدية للأغراض الأكاديمية، وبرامج الانغماس الثقافي. كل دورة تشمل اختبارات تفاعلية ومواد دراسية ومتابعة التقدم.',
                'category' => 'courses',
                'priority' => 8
            ],
            
            // Livres et matériaux
            [
                'keyword' => 'books,materials,study,كتب,مواد,دراسة',
                'question_en' => 'What books and study materials do you have?',
                'question_ar' => 'ما هي الكتب والمواد الدراسية التي لديكم؟',
                'response_en' => 'We have a comprehensive collection of Swedish language books including textbooks, grammar guides, vocabulary books, and cultural materials. Our digital library includes interactive e-books, audio materials, and supplementary resources to enhance your learning experience.',
                'response_ar' => 'لدينا مجموعة شاملة من كتب اللغة السويدية تشمل الكتب المدرسية وأدلة القواعد وكتب المفردات والمواد الثقافية. مكتبتنا الرقمية تشمل كتب إلكترونية تفاعلية ومواد صوتية وموارد إضافية لتعزيز تجربة التعلم.',
                'category' => 'books',
                'priority' => 8
            ],
            
            // Quiz et évaluations
            [
                'keyword' => 'quiz,test,exam,assessment,اختبار,امتحان,تقييم',
                'question_en' => 'How do quizzes and assessments work?',
                'question_ar' => 'كيف تعمل الاختبارات والتقييمات؟',
                'response_en' => 'Our quiz system includes interactive assessments for each course level. You can take quizzes to test your knowledge, track your progress, and receive immediate feedback. Quizzes cover grammar, vocabulary, reading comprehension, and listening skills. Your results are saved and you can retake quizzes to improve your scores.',
                'response_ar' => 'نظام الاختبارات لدينا يشمل تقييمات تفاعلية لكل مستوى دورة. يمكنك أخذ اختبارات لاختبار معرفتك ومتابعة تقدمك والحصول على تغذية راجعة فورية. الاختبارات تغطي القواعد والمفردات والفهم القرائي ومهارات الاستماع. يتم حفظ نتائجك ويمكنك إعادة الاختبارات لتحسين درجاتك.',
                'category' => 'quiz',
                'priority' => 7
            ],
            
            // Comment passer un quiz
            [
                'keyword' => 'how,pass,quiz,take,test,كيف,اجتياز,اختبار',
                'question_en' => 'How do I take a quiz?',
                'question_ar' => 'كيف يمكنني أخذ اختبار؟',
                'response_en' => 'To take a quiz: 1) Log into your account, 2) Go to your enrolled course, 3) Click on "Start Quiz" for the desired topic, 4) Answer all questions within the time limit, 5) Submit your answers to see your results immediately. You can retake quizzes to improve your score.',
                'response_ar' => 'لأخذ اختبار: 1) سجل دخولك إلى حسابك، 2) اذهب إلى دورتك المسجلة، 3) انقر على "ابدأ الاختبار" للموضوع المطلوب، 4) أجب على جميع الأسئلة في الوقت المحدد، 5) أرسل إجاباتك لرؤية نتائجك فوراً. يمكنك إعادة الاختبارات لتحسين درجتك.',
                'category' => 'quiz',
                'priority' => 7
            ],
            
            // Prix et paiement
            [
                'keyword' => 'price,cost,fee,payment,pay,سعر,تكلفة,رسوم,دفع',
                'question_en' => 'How much do the courses cost?',
                'question_ar' => 'كم تكلفة الدورات؟',
                'response_en' => 'Our course prices vary depending on the level and duration. Beginner courses start from $299, Intermediate from $399, and Advanced from $499. Books and study materials are included. We also offer special packages and discounts for students. Please contact us for detailed pricing information.',
                'response_ar' => 'تختلف أسعار دوراتنا حسب المستوى والمدة. تبدأ دورات المبتدئين من 299 دولار، والمتوسطة من 399 دولار، والمتقدمة من 499 دولار. الكتب والمواد الدراسية مشمولة. كما نقدم حزم خاصة وخصومات للطلاب. يرجى الاتصال بنا للحصول على معلومات مفصلة عن الأسعار.',
                'category' => 'pricing',
                'priority' => 7
            ],
            
            // Inscription
            [
                'keyword' => 'register,enroll,sign up,inscription,تسجيل,انضمام',
                'question_en' => 'How can I register for a course?',
                'question_ar' => 'كيف يمكنني التسجيل في دورة؟',
                'response_en' => 'You can register for our courses through our website, by phone, or by visiting our academy. The registration process is simple: choose your course, fill out the registration form, and complete the payment. We\'ll then send you confirmation and access to your course materials, books, and quizzes.',
                'response_ar' => 'يمكنك التسجيل في دوراتنا من خلال موقعنا الإلكتروني، أو عبر الهاتف، أو بزيارة أكاديميتنا. عملية التسجيل بسيطة: اختر دورتك، املأ نموذج التسجيل، وأكمل الدفع. سنرسل لك بعد ذلك تأكيداً ووصولاً لمواد دورتك وكتبك واختباراتك.',
                'category' => 'registration',
                'priority' => 6
            ],
            
            // Horaires
            [
                'keyword' => 'schedule,time,when,classes,جدول,وقت,متى,فصول',
                'question_en' => 'What are the class schedules?',
                'question_ar' => 'ما هي جداول الفصول؟',
                'response_en' => 'We offer flexible scheduling options including morning, afternoon, and evening classes. Classes are available Monday through Friday, and we also have weekend options. Each class session is 2 hours long, and you can choose between group classes or private lessons. Online quizzes and study materials are available 24/7.',
                'response_ar' => 'نقدم خيارات جدولة مرنة تشمل فصول الصباح والظهر والمساء. الفصول متاحة من الاثنين إلى الجمعة، ولدينا أيضاً خيارات عطلة نهاية الأسبوع. كل جلسة فصل مدتها ساعتان، ويمكنك الاختيار بين فصول المجموعة أو الدروس الخاصة. الاختبارات الإلكترونية والمواد الدراسية متاحة على مدار الساعة.',
                'category' => 'schedule',
                'priority' => 5
            ],
            
            // Certificats
            [
                'keyword' => 'certificate,certification,diploma,شهادة,إجازة',
                'question_en' => 'Do you provide certificates?',
                'question_ar' => 'هل تقدمون شهادات؟',
                'response_en' => 'Yes, we provide official certificates upon completion of each course level. Our certificates are recognized and can be used for academic or professional purposes. We also prepare students for official Swedish language proficiency tests. Your quiz results and progress are included in your final assessment.',
                'response_ar' => 'نعم، نقدم شهادات رسمية عند إكمال كل مستوى دورة. شهاداتنا معترف بها ويمكن استخدامها للأغراض الأكاديمية أو المهنية. كما نعد الطلاب لاختبارات الكفاءة الرسمية في اللغة السويدية. نتائج اختباراتك وتقدمك مشمولة في تقييمك النهائي.',
                'category' => 'certification',
                'priority' => 4
            ],
            
            // Support et aide
            [
                'keyword' => 'help,support,assistance,مساعدة,دعم',
                'question_en' => 'I need help with my course',
                'question_ar' => 'أحتاج مساعدة في دورتي',
                'response_en' => 'I\'m here to help! You can ask me about course content, quiz questions, study materials, or technical issues. For personalized assistance, you can also contact our support team or schedule a consultation with one of our teachers. Your progress and quiz results are tracked to provide better support.',
                'response_ar' => 'أنا هنا لمساعدتك! يمكنك سؤالي عن محتوى الدورة أو أسئلة الاختبار أو المواد الدراسية أو المشاكل التقنية. للمساعدة الشخصية، يمكنك أيضاً الاتصال بفريق الدعم لدينا أو جدولة استشارة مع أحد معلمينا. يتم تتبع تقدمك ونتائج اختباراتك لتقديم دعم أفضل.',
                'category' => 'help',
                'priority' => 6
            ],
            
            // Contact
            [
                'keyword' => 'contact,phone,email,address,اتصال,هاتف,بريد,عنوان',
                'question_en' => 'How can I contact you?',
                'question_ar' => 'كيف يمكنني الاتصال بكم؟',
                'response_en' => 'You can contact us by phone at +46 123 456 789, by email at info@swedishacademy.se, or visit us at our address. We\'re available Monday to Friday from 9 AM to 6 PM. You can also use our contact form on the website or send me a message here for immediate assistance.',
                'response_ar' => 'يمكنك الاتصال بنا عبر الهاتف على +46 123 456 789، أو عبر البريد الإلكتروني على info@swedishacademy.se، أو زيارتنا في عنواننا. نحن متاحون من الاثنين إلى الجمعة من 9 صباحاً إلى 6 مساءً. يمكنك أيضاً استخدام نموذج الاتصال على موقعنا أو إرسال رسالة لي هنا للمساعدة الفورية.',
                'category' => 'contact',
                'priority' => 3
            ],
            
            // Questions fréquentes
            [
                'keyword' => 'faq,help,support,help,مساعدة,دعم',
                'question_en' => 'I need help',
                'question_ar' => 'أحتاج مساعدة',
                'response_en' => 'I\'m here to help! You can ask me about our courses, books, quizzes, registration, pricing, schedules, or any other questions about Swedish Academy. If I can\'t answer your question, I\'ll connect you with our support team.',
                'response_ar' => 'أنا هنا لمساعدتك! يمكنك سؤالي عن دوراتنا وكتبنا واختباراتنا والتسجيل والأسعار والجداول أو أي أسئلة أخرى حول الأكاديمية السويدية. إذا لم أتمكن من الإجابة على سؤالك، سأربطك بفريق الدعم لدينا.',
                'category' => 'help',
                'priority' => 2
            ],
            
            // Au revoir
            [
                'keyword' => 'bye,goodbye,see you,thank you,شكراً,وداعاً,مع السلامة',
                'question_en' => 'Thank you and goodbye',
                'question_ar' => 'شكراً لك وإلى اللقاء',
                'response_en' => 'You\'re welcome! Thank you for choosing Swedish Academy. If you have any more questions about our courses, books, or quizzes in the future, feel free to ask. Have a great day!',
                'response_ar' => 'على الرحب والسعة! شكراً لك على اختيار الأكاديمية السويدية. إذا كان لديك أي أسئلة أخرى حول دوراتنا أو كتبنا أو اختباراتنا في المستقبل، لا تتردد في السؤال. أتمنى لك يوماً سعيداً!',
                'category' => 'farewell',
                'priority' => 1
            ]
        ];

        foreach ($responses as $response) {
            ChatBotResponse::create($response);
        }
    }
}
