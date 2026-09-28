<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Package;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAIChatbotService
{
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;
    protected string $systemPrompt;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('services.openai.max_tokens', 4000);
        $this->systemPrompt = $this->buildSystemPrompt();
    }

    /**
     * Fetch courses from database with caching (refreshed every 30 minutes)
     */
    protected function getCoursesContext(): string
    {
        return Cache::remember('chatbot_courses_context', 1800, function () {
            $context = "";

            // Fetch all active/listed products (courses)
            $products = Product::with(['variations', 'category', 'teacher'])
                ->where('is_listed', true)
                ->get();

            if ($products->isNotEmpty()) {
                $context .= "\n\n## الدورات المتاحة حالياً من قاعدة البيانات:\n";

                $addedCourses = []; // Track added courses to avoid duplicates

                foreach ($products as $product) {
                    // Get Arabic variation first, then English, then any
                    $variation = $product->variations()->where('langue', 'ar')->first()
                        ?? $product->variations()->where('langue', 'en')->first()
                        ?? $product->variations()->first();

                    if (!$variation) continue;

                    $name = $variation->name ?? 'غير محدد';

                    // Skip if already added
                    if (in_array($name, $addedCourses)) continue;
                    $addedCourses[] = $name;

                    $description = $variation->short_description ?? '';
                    $price = $product->prix;
                    $duration = $product->period ?? $product->duration ?? '';
                    $category = $product->category->name ?? '';
                    $teacherName = $product->teacher ? ($product->teacher->nom . ' ' . $product->teacher->prenom) : '';

                    // Determine course type
                    $types = [];
                    if ($product->is_online) $types[] = 'أونلاين';
                    if ($product->is_zoom) $types[] = 'زووم';
                    if ($product->is_classroom) $types[] = 'حضوري';
                    $typeStr = implode(' / ', $types) ?: 'غير محدد';

                    $context .= "\n{$name}";
                    if ($category) $context .= " ({$category})";
                    $context .= "\n  النمط: {$typeStr}";
                    if ($duration) $context .= " | المدة: {$duration}";
                    if ($price > 0) {
                        $context .= " | السعر: {$price} دولار";
                    } else {
                        $context .= " | مجانية";
                    }
                    if ($teacherName) {
                        $context .= "\n  المحاضر: {$teacherName}";
                    }
                    if ($description) {
                        $shortDesc = mb_substr(strip_tags($description), 0, 150);
                        $context .= "\n  الوصف: {$shortDesc}";
                    }
                    $context .= "\n";
                }
            }

            // Fetch all active packages
            $packages = Package::with(['packageProducts.product.variations'])
                ->where('is_active', true)
                ->get();

            if ($packages->isNotEmpty()) {
                $context .= "\n\n## الحزم التدريبية المتاحة:\n";

                foreach ($packages as $package) {
                    $title = $package->title_ar ?: $package->title;
                    $description = $package->description_ar ?: $package->description;
                    $productsCount = $package->packageProducts->count();

                    $context .= "\n• {$title}";
                    $context .= " (تتضمن {$productsCount} دورات)";

                    // List included courses
                    $courseNames = [];
                    foreach ($package->packageProducts as $pp) {
                        if ($pp->product && $pp->is_active) {
                            $pVar = $pp->product->variations()->where('langue', 'ar')->first()
                                ?? $pp->product->variations()->first();
                            if ($pVar) {
                                $courseNames[] = $pVar->name;
                            }
                        }
                    }
                    if (!empty($courseNames)) {
                        $context .= "\n  تشمل: " . implode('، ', $courseNames);
                    }

                    if ($description) {
                        $shortDesc = mb_substr(strip_tags($description), 0, 100);
                        $context .= "\n  الوصف: {$shortDesc}";
                    }
                    $context .= "\n";
                }
            }

            return $context;
        });
    }

    /**
     * Build the system prompt with Upskills Academy training data
     */
    protected function buildSystemPrompt(): string
    {
        // Get dynamic courses context from database
        $coursesContext = $this->getCoursesContext();

        $basePrompt = <<<'PROMPT'
أنت مساعد Upskills، الشاتبوت الذكي لأكاديمية أب سكيلز لتعليم اللغة الإنجليزية (Upskills Academy). أجب بلغة المستخدم (عربي/إنجليزي/فرنسي).

⚠️ قواعد صارمة - اتبعها بدقة:
1. اقرأ رسالة المستخدم الأخيرة بعناية شديدة وأجب عليها تحديداً. لا تكرر إجابات سابقة إذا سأل سؤالاً مختلفاً.
2. إذا قال المستخدم أنه لم يسأل عن شيء معين أو طلب شيئاً مختلفاً، اعتذر واسأله بوضوح: "عذراً على سوء الفهم! ما الذي تريد معرفته بالتحديد؟"
3. أجب على الأسئلة المتعلقة بأكاديمية أب سكيلز ودورات اللغة الإنجليزية والمعلمين والتسجيل
4. لأي سؤال خارج النطاق تماماً (مثل الطبخ، السياسة، الرياضيات)، أجب: "عذراً، أنا مختص بأكاديمية أب سكيلز فقط. كيف أساعدك بخصوص دوراتنا؟"
5. كن صبوراً مع الأخطاء الإملائية - افهم قصد المستخدم
6. حافظ على سياق المحادثة - لكن إذا أوضح المستخدم أنك أخطأت في الفهم، صحح مسارك فوراً
7. عند السؤال عن دورة معينة: أعط اسمها الكامل، النمط، المدة، السعر، المحتوى التعليمي
8. شجع على التسجيل في الدورات والحزم (Upselling ذكي)
9. إذا لم تفهم سؤال المستخدم، اطلب التوضيح: "عذراً، لم أفهم سؤالك بشكل واضح. هل يمكنك إعادة صياغته؟"
10. كلمات مثل "أريد" أو "نعم" أو "لا" وحدها تعني أن المستخدم يتابع المحادثة - راجع السياق وأجب بناءً على آخر موضوع.
11. عبارات مثل "أعطني المزيد" أو "معلومات أكثر عنها/عنه" أو "أخبرني أكثر" تشير إلى الموضوع الرئيسي الذي كنت تتحدث عنه (عادة الدورة). أعط تفاصيل إضافية عن نفس الدورة مثل: المحتوى التفصيلي، المتطلبات، طريقة الاختبار، لا تنتقل لموضوع آخر إلا إذا طلب ذلك صراحة.

## معلومات الأكاديمية
الاسم: أكاديمية أب سكيلز (Upskills Academy) — أكاديمية متخصصة في تعليم اللغة الإنجليزية
الرؤية: أن نكون المرجع الأول لتعلّم الإنجليزية بمسارات منظمة من المستوى A1 حتى الاحتراف
الموقع: www.upskillsacademy.com
البريد: info@up-skills.academy
الهاتف/واتساب: +44 20 7946 0958
العنوان: 12 Learning Street, London, United Kingdom

## المعايير والاعتمادات
• جميع المستويات متوافقة مع الإطار الأوروبي المرجعي المشترك للغات (CEFR): A1 → C1
• برنامج تدريب المعلمين متوافق مع معايير TESOL الدولية
• شهادات إتمام موثقة برقم تسلسلي ورمز QR يمكن التحقق منها عبر الموقع

## أنماط الدراسة
1. أونلاين (Online): دراسة ذاتية 24/7 بمحتوى مسجل وتمارين تفاعلية
2. زووم (Live on Zoom): حصص مباشرة مع المعلم + تدريب على المحادثة
3. حضوري (Classroom): مجموعات صغيرة (خاصة للأطفال والناشئين)
4. مدمج (Blended Learning): أونلاين + حصص مباشرة، أفضل تجربة شاملة
5. CPD (تطوير المعلمين): برامج تطوير مهني لمعلمي الإنجليزية
6. مجاني (Free): نادي المحادثة الأسبوعي المجاني — متاح للجميع

## الدورات الرئيسية
1. الإنجليزية العامة A1 (مبتدئ) — 99$ — 3 أشهر — لا متطلبات مسبقة
2. الإنجليزية العامة A2 (أساسي) — 99$ — 3 أشهر
3. الإنجليزية العامة B1 (متوسط) — 129$ — 3 أشهر
4. الإنجليزية العامة B2 (فوق المتوسط) — 149$ — 4 أشهر
5. أساسيات الإنجليزية للأعمال (Business English) — 199$ — شهران — رسائل، اجتماعات، مفاوضات، مقابلات
6. التحضير لاختبار الآيلتس IELTS — 249$ — 8 أسابيع زووم مباشر — استهداف Band 7+
7. التحضير لاختبار التوفل TOEFL iBT — 249$ — 8 أسابيع زووم مباشر — استهداف +100
8. نادي المحادثة الإنجليزية — مجاني — جلسات أسبوعية لجميع المستويات
9. دورة القواعد المتقدمة (Grammar Masterclass) — 79$ — 6 أسابيع
10. مهارات الكتابة الأكاديمية — 119$ — 6 أسابيع
11. الإنجليزية للأطفال والناشئين — 159$ — 3 أشهر حضوري
12. شهادة تدريب معلمي الإنجليزية TESOL — 299$ — 10 أسابيع — تشمل تدريباً عملياً مُقيَّماً
💡 لتحديد مستواك: ابدأ باختبار تحديد المستوى أو استشر فريقنا!

## الحزم التدريبية - توفير 15-20%
1. حزمة الإتقان الكامل (A1→B2): المستويات الأربعة بخصم 20%
2. حزمة النجاح في الاختبارات: آيلتس + توفل + الكتابة الأكاديمية بخصم 15%
3. حزمة الإنجليزية المهنية: إنجليزية الأعمال + القواعد + TESOL بخصم 15%
💰 الحزم "استثمار ذكي": مسار أشمل بسعر أقل!

## نظام الاختبارات
الكويز (Quiz) لكل وحدة:
• أسئلة اختيار من متعدد
• 60% للنجاح
• حتى 3 محاولات

الاختبار النهائي النظري:
• يُفتح بعد إتمام وحدات الدورة
• أسئلة عشوائية، تصحيح فوري

اختبار المحادثة (حسب النمط):
• أونلاين: تسجيل فيديو والرد على موضوع محدد
• زووم: مقابلة مباشرة مع المعلم وتقييم يحاكي الممتحنين الرسميين

## الشهادات
• ترسل إلكترونياً فور النجاح عبر البريد
• تُخزن في بروفايل الطالب للوصول الدائم
• يمكن التحقق منها عبر الرقم التسلسلي وQR Code على الموقع
• متوافقة مع مستويات CEFR

## طرق الدفع
• بطاقة فيزا / ماستركارد (Stripe)
• تحويل بنكي أو إيصال دفع يُراجع من الإدارة
• الأقساط: متاحة للدورات 199$ فما فوق (التفاصيل في صفحة الدورة)

## نظام النقاط والمكافآت
• كل 1 دولار = نقاط تُجمع في رصيدك
• النقاط تُستخدم كخصم على مشترياتك القادمة
برنامج الإحالة:
• شارك رابط الإحالة الخاص بك
• عند شراء صديقك أول دورة: تحصل على مكافأة + صديقك يحصل على خصم
⚠️ ملاحظة: لا يمكن الجمع بين كوبون خصم ونقاط في طلب واحد

## لوحة تحكم الطالب
الملف الشخصي، دوراتي، كتبي، شهاداتي، طلباتي، التقويم، جدول الحصص، تاريخ الاختبارات، اختبارات المحادثة، جلسات Zoom، الدعم والتذاكر، العروض الخاصة

## إجابات سريعة
• لغة الدراسة: الدورات تُدرّس بالإنجليزية مع دعم بالعربية للمستويات المبتدئة
• الدراسة من أي دولة: نعم، بشرط توفر إنترنت
• المنصة على الهاتف: نعم، متوافقة مع الهواتف الذكية والتابلت
• المنصة متاحة: 24/7 للدراسة في أي وقت
• نسيان كلمة المرور: استخدم "نسيت كلمة المرور" من صفحة تسجيل الدخول
• للمبتدئين: جرّب نادي المحادثة المجاني أولاً!
• لست متأكداً من مستواك؟ تواصل معنا لاختبار تحديد المستوى

## قواعد التنسيق للإجابات
⚠️ ممنوع: النجوم (** أو *)، الشرطات (-) في بداية السطر، أي رموز markdown
✅ استخدم: نص عادي، أرقام (1. 2. 3.) للقوائم، رمز (•) للنقاط الفرعية، أسطر فارغة بين الفقرات
✅ كن مختصراً: إجابات واضحة ومباشرة بدون تكرار أو اقتطاع
✅ أكمل إجابتك: لا تقطع الجملة في المنتصف، اكتب الإجابة كاملة
✅ عند ذكر دورة: اذكر اسمها مرة واحدة فقط مع كامل التفاصيل
✅ كن ودوداً: استخدم لغة مهنية لكن ودية
PROMPT;

        // Append dynamic courses context from database
        return $basePrompt . $coursesContext;
    }

    /**
     * Clear the cached courses context (call this when courses are updated)
     */
    public static function clearCoursesCache(): void
    {
        Cache::forget('chatbot_courses_context');
    }

    /**
     * Send a message to OpenAI and get a response
     */
    public function chat(string $message, array $conversationHistory = []): array
    {
        try {
            // Build messages array
            $messages = [
                ['role' => 'system', 'content' => $this->systemPrompt]
            ];

            // Add conversation history (last 20 messages for better context)
            $recentHistory = array_slice($conversationHistory, -20);
            foreach ($recentHistory as $historyItem) {
                $messages[] = [
                    'role' => $historyItem['role'],
                    'content' => $historyItem['content']
                ];
            }

            // Add current user message
            $messages[] = ['role' => 'user', 'content' => $message];

            // Make API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $this->maxTokens,
                'temperature' => 0.7,
                'presence_penalty' => 0.2,
                'frequency_penalty' => 0.2,
                'top_p' => 0.95,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $assistantMessage = $data['choices'][0]['message']['content'] ?? '';

                // Clean markdown formatting from response
                $cleanMessage = $this->cleanMarkdown($assistantMessage);

                return [
                    'success' => true,
                    'message' => trim($cleanMessage),
                    'usage' => $data['usage'] ?? null,
                ];
            } else {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => $this->getFallbackResponse($message),
                    'error' => 'API request failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Chatbot Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $this->getFallbackResponse($message),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get a fallback response if API fails
     */
    protected function getFallbackResponse(string $message): string
    {
        $isArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $message);

        if ($isArabic) {
            return 'عذراً، نواجه مشكلة تقنية حالياً. يرجى المحاولة مرة أخرى لاحقاً أو التواصل معنا عبر البريد الإلكتروني: info@up-skills.academy';
        }

        return 'Sorry, we are experiencing technical difficulties. Please try again later or contact us at: info@up-skills.academy';
    }

    /**
     * Get quick replies/suggestions based on context
     */
    public function getQuickReplies(string $language = 'en'): array
    {
        if ($language === 'ar') {
            return [
                'ما هي الدورات المتاحة؟',
                'ما شروط التسجيل في دورة المدرب الشخصي؟',
                'هل توجد دورات مجانية؟',
                'ما هي الاعتمادات الدولية؟',
                'كيف أتواصل مع الدعم؟',
            ];
        }

        if ($language === 'fr') {
            return [
                'Quels cours sont disponibles?',
                'Quelles sont les conditions d\'inscription?',
                'Y a-t-il des cours gratuits?',
                'Quelles sont les accréditations?',
                'Comment contacter le support?',
            ];
        }

        return [
            'What courses are available?',
            'What are the requirements for Personal Trainer course?',
            'Are there any free courses?',
            'What are the international accreditations?',
            'How can I contact support?',
        ];
    }

    /**
     * Get welcome message
     */
    public function getWelcomeMessage(string $language = 'en'): string
    {
        if ($language === 'ar') {
            return '👋 مرحباً بك في أكاديمية أب سكيلز! أنا مساعدك الذكي وسأساعدك في الاستفسار عن الدورات، الاعتمادات، التسجيل، وكل ما يخص الأكاديمية. كيف يمكنني مساعدتك اليوم؟';
        }

        if ($language === 'fr') {
            return '👋 Bienvenue à Upskills Academy ! Je suis votre assistant IA. Comment puis-je vous aider aujourd\'hui?';
        }

        return '👋 Welcome to Upskills Academy! I\'m your AI assistant and I\'m here to help you with English courses, levels, registration, certificates, and more. How can I help you today?';
    }

    /**
     * Clean markdown formatting from AI response
     */
    protected function cleanMarkdown(string $text): string
    {
        // Remove bold markdown (**text** or __text__)
        $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text);
        $text = preg_replace('/__(.+?)__/', '$1', $text);

        // Remove italic markdown (*text* or _text_)
        $text = preg_replace('/\*(.+?)\*/', '$1', $text);
        $text = preg_replace('/_(.+?)_/', '$1', $text);

        // Replace dash bullet points with bullet symbol
        $text = preg_replace('/^(\s*)- /m', '$1• ', $text);

        // Remove code blocks
        $text = preg_replace('/```[\s\S]*?```/', '', $text);
        $text = preg_replace('/`(.+?)`/', '$1', $text);

        // Remove headers (# ## ### etc)
        $text = preg_replace('/^#+\s*/m', '', $text);

        // Clean up extra whitespace
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
