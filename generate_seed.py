#!/usr/bin/env python3
"""Generate seed_upskills_demo.sql — converts the sast DB content from
sport-coaching (SAST) to English-teaching (Upskills Academy) demo data.
A pre-rebrand backup exists: backup_sast_pre_rebrand.sql.gz"""

import os

# bcrypt hashes of DEMO_TEACHER_PASSWORD / DEMO_STUDENT_PASSWORD (plaintext kept in the root .env)
TEACHER_HASH = "$2y$10$Re9xHpY7Fv5khLPpzIDQGuCSRTxU//E8VEiG489MviqoZ776WMIvu"
STUDENT_HASH = "$2y$10$3Gvyzwu4/Rani83JUC3epeZykpx5LukRiJ8VERe.knb22Ys/UxBGy"

def q(s):
    if s is None: return "NULL"
    return "'" + str(s).replace("\\", "\\\\").replace("'", "''") + "'"

out = []
w = out.append

w("-- Upskills Academy demo seed — generated 2026-07-18")
w("-- Converts sport-coaching content to English-teaching demo content.")
w("SET NAMES utf8mb4;")
w("SET FOREIGN_KEY_CHECKS=0;")
w("START TRANSACTION;")

# ---------------------------------------------------------------- truncates
truncate = [
    # catalog + assessment
    "products","product_variations","product_studies","quiz_products","quizzes",
    "questions","reponse_questions","sujets","training_cases","product_training_cases",
    "training_case_files","course_sessions","zoom_meetings","content_milestones",
    "certifs","certif_students","books","packages","package_products","resources",
    # people
    "teachers","students",
    # cms / marketing
    "avis_acceuil","carousels","products_acceuil","nos_partenaires","blogs","articles",
    "coupons","coupon_detailles","coupon_category","partnerships","partnership_courses",
    # engagement referencing old content
    "discussions","response_discussions","course_ratings","student_successes",
    "student_stage_courses","stage_documents","student_video_exams",
    # PII / junk (analysis: retained after user purge)
    "email_logs","notifications","chat_messages","chat_conversations","contact_messages",
    "internal_messages","password_reset_codes","user_activities","failed_jobs","carts",
    "newsletter_subscriptions","email_messages","email_threads","anis_test",
    "abandoned_carts","abandoned_cart_items",
]
for t in truncate:
    w(f"TRUNCATE TABLE `{t}`;")

# ---------------------------------------------------------------- categories (update in place)
cats = [
    (1, "Live on Zoom", "Live on Zoom", "مباشر عبر زووم"),
    (2, "Online", "Online", "أونلاين"),
    (3, "Classroom", "Classroom", "حضوري"),
    (4, "Free", "Free", "مجاني"),
    (5, "Blended Learning", "Blended Learning", "تعلّم مدمج"),
    (6, "CPD — Teacher Development", "CPD — Teacher Development", "تطوير المعلمين CPD"),
    (8, "Proficiency Exams (PCE)", "Proficiency Exams (PCE)", "اختبارات الكفاءة"),
]
for cid, t, te, ta in cats:
    w(f"UPDATE categories SET titre={q(t)}, titre_en={q(te)}, titre_ar={q(ta)} WHERE id={cid};")

# ---------------------------------------------------------------- teachers
teachers = [
    ("ميتشل","سارة","Mitchell","Sarah"), ("كارتر","جيمس","Carter","James"),
    ("واتسون","إميلي","Watson","Emily"), ("بروكس","دانيال","Brooks","Daniel"),
    ("بينيت","أوليفيا","Bennett","Olivia"), ("ريد","مايكل","Reed","Michael"),
    ("خليل","أمينة","Khalil","Amina"), ("حداد","عمر","Haddad","Omar"),
]
for i,(nom,prenom,nom_en,prenom_en) in enumerate(teachers,1):
    email = f"{prenom_en.lower()}.{nom_en.lower()}@upskillsacademy.com"
    w(f"INSERT INTO teachers (id,nom,prenom,email,images,password,nom_en,prenom_en,created_at,updated_at) VALUES "
      f"({i},{q(nom)},{q(prenom)},{q(email)},'uploads/teachers/t{i}.svg',{q(TEACHER_HASH)},{q(nom_en)},{q(prenom_en)},NOW(),NOW());")

# ---------------------------------------------------------------- demo student
w(f"INSERT INTO students (id,first_name,last_name,email,password,email_verified_at,created_at,updated_at) VALUES "
  f"(1,'Demo','Student','demo@upskillsacademy.com',{q(STUDENT_HASH)},NOW(),NOW(),NOW());")

# ---------------------------------------------------------------- products
w("SET @cid := (SELECT MIN(id) FROM countries);")
# (image, cat, teacher, type, prix, period, online, zoom, classroom, installment, theo_exam, prac_exam)
P = [
    ("course-a1.svg",2,1,"fa", 99,"3 months",1,0,0,0,0,0),
    ("course-a2.svg",2,2,"fa", 99,"3 months",1,0,0,0,0,0),
    ("course-b1.svg",2,3,"fi",129,"3 months",1,0,0,0,0,0),
    ("course-b2.svg",2,4,"fi",149,"4 months",1,0,0,0,0,0),
    ("course-business.svg",2,5,"pt",199,"2 months",1,0,0,1,0,0),
    ("course-ielts.svg",1,6,"pt",249,"8 weeks",0,1,0,1,1,0),
    ("course-toefl.svg",1,6,"pt",249,"8 weeks",0,1,0,1,1,0),
    ("course-conversation.svg",4,7,"fa",0,"ongoing",1,0,0,0,0,0),
    ("course-grammar.svg",2,3,"fi", 79,"6 weeks",1,0,0,0,0,0),
    ("course-writing.svg",2,4,"fi",119,"6 weeks",1,0,0,0,0,0),
    ("course-kids.svg",3,8,"fa",159,"3 months",0,0,1,0,0,0),
    ("course-tesol.svg",6,1,"pt",299,"10 weeks",1,0,0,1,1,1),
]
for i,(img,cat,tch,tc,prix,period,onl,zm,cls,inst,theo,prac) in enumerate(P,1):
    pet = "'online'" if prac else "NULL"
    w(f"INSERT INTO products (id,iscach,categories_id,period,point,image,promo_points,statut,online,classroom,"
      f"teacher_id,country_id,certif_id,certificate_generation_mode,type_course,goverrnement,prix,is_listed,"
      f"max_exam_attempts,has_theoretical_exam,has_practical_exam,practical_exam_type,renewal_price,"
      f"installment_allowed,validity_months,is_exam_video,is_stage,is_classroom,is_zoom,is_online,breuillant,created_at,updated_at) VALUES "
      f"({i},1,{cat},{q(period)},{prix*10},{q(img)},0,1,{onl},{cls},{tch},@cid,NULL,'manual',{q(tc)},'0',{prix}.00,1,"
      f"3,{theo},{prac},{pet},50.00,{inst},12,0,0,{cls},{zm},{onl},0,NOW(),NOW());")

# ---------------------------------------------------------------- variations (en + ar)
V = [
 ("General English — Beginner (A1)","اللغة الإنجليزية العامة — مبتدئ (A1)",
  "Start speaking English from day one. No previous knowledge needed.",
  "ابدأ التحدث بالإنجليزية من اليوم الأول، دون الحاجة لأي معرفة سابقة.",
  "A friendly, structured start to English: alphabet and sounds, essential grammar, everyday vocabulary and guided speaking practice in every lesson. By the end you will handle simple conversations, introduce yourself and understand basic written English.",
  "بداية منظمة وودية لتعلّم الإنجليزية: الحروف والأصوات، القواعد الأساسية، مفردات الحياة اليومية وتدريب موجّه على المحادثة في كل درس. في نهاية الدورة ستتمكن من إجراء محادثات بسيطة وتقديم نفسك وفهم النصوص الأساسية."),
 ("General English — Elementary (A2)","اللغة الإنجليزية العامة — أساسي (A2)",
  "Build everyday fluency: routines, travel, shopping and short stories.",
  "طوّر طلاقتك اليومية: الروتين والسفر والتسوق والقصص القصيرة.",
  "Consolidate the basics and expand into real situations: past tenses, comparisons, travel language and story-telling, with speaking labs every week to turn knowledge into fluent use.",
  "رسّخ الأساسيات وتوسّع نحو مواقف واقعية: أزمنة الماضي، المقارنات، لغة السفر وسرد القصص، مع مختبرات محادثة أسبوعية لتحويل المعرفة إلى استخدام طليق."),
 ("General English — Intermediate (B1)","اللغة الإنجليزية العامة — متوسط (B1)",
  "Communicate with confidence in work, travel and social life.",
  "تواصل بثقة في العمل والسفر والحياة الاجتماعية.",
  "Move from understanding to expressing: perfect tenses in context, future plans, opinion language and phrasal verbs, plus listening built from real conversations and weekly debate practice.",
  "انتقل من الفهم إلى التعبير: الأزمنة التامة في سياقها، خطط المستقبل، لغة إبداء الرأي والأفعال المركبة، مع تدريبات استماع من محادثات واقعية ومناظرات أسبوعية."),
 ("General English — Upper-Intermediate (B2)","اللغة الإنجليزية العامة — فوق المتوسط (B2)",
  "Refine your English: nuance, idioms, essays and presentations.",
  "صقل لغتك الإنجليزية: الدقة والتعابير والمقالات والعروض التقديمية.",
  "Push towards advanced mastery: full tense control, conditionals, idiomatic language, press reading, structured essay writing and confident presenting — the springboard to C1 and exam courses.",
  "اندفع نحو الإتقان المتقدم: تحكم كامل في الأزمنة، الجمل الشرطية، اللغة الاصطلاحية، قراءة الصحافة، كتابة المقالات المنظمة والعرض بثقة — نقطة الانطلاق نحو C1 ودورات الاختبارات."),
 ("Business English Essentials","أساسيات الإنجليزية للأعمال",
  "Professional emails, meetings, negotiations and interviews.",
  "رسائل مهنية واجتماعات ومفاوضات ومقابلات عمل.",
  "Hands-on business communication: write emails that get answers, lead meetings, negotiate, present with impact and shine in interviews — finished with a realistic deal-closing case study.",
  "تواصل مهني تطبيقي: اكتب رسائل تحصل على ردود، أدر الاجتماعات، فاوض، قدّم عروضًا مؤثرة وتألّق في المقابلات — وتُختتم الدورة بدراسة حالة واقعية لإتمام صفقة."),
 ("IELTS Exam Preparation","التحضير لاختبار الآيلتس IELTS",
  "A focused 8-week path to Band 7+, with full mock tests.",
  "مسار مركّز لمدة 8 أسابيع نحو المستوى +7 مع اختبارات تجريبية كاملة.",
  "Live Zoom classes covering all four IELTS papers: proven strategies for Listening and Reading, high-scoring frameworks for Writing Tasks 1 & 2, and Speaking practice with examiner-style feedback, ending in a full timed mock exam.",
  "حصص مباشرة عبر زووم تغطي أقسام الآيلتس الأربعة: استراتيجيات مجرّبة للاستماع والقراءة، وأطر عالية الدرجات لمهمتي الكتابة 1 و2، وتدريب على المحادثة بتقييم يحاكي الممتحنين، وتُختتم باختبار تجريبي كامل بتوقيت رسمي."),
 ("TOEFL iBT Preparation","التحضير لاختبار التوفل TOEFL iBT",
  "Master the TOEFL iBT format and target 100+.",
  "أتقن صيغة التوفل iBT واستهدف نتيجة +100.",
  "Section-by-section mastery of the TOEFL iBT: academic reading, lecture listening with note-taking systems, integrated speaking templates and both essay types, ending in a full simulated test with personal feedback.",
  "إتقان قسمًا بقسم لاختبار التوفل iBT: القراءة الأكاديمية، الاستماع للمحاضرات مع أنظمة تدوين الملاحظات، قوالب المحادثة المتكاملة ونوعا المقال، وتُختتم باختبار محاكاة كامل مع تقييم شخصي."),
 ("English Conversation Club","نادي المحادثة الإنجليزية",
  "Free weekly speaking practice — all levels welcome.",
  "تدريب مجاني أسبوعي على المحادثة — جميع المستويات مرحّب بها.",
  "A free, friendly space to actually speak: icebreakers, debates, role-plays, movie and music sessions, guided by our teachers. Join any week, at any level, and build real confidence.",
  "مساحة مجانية وودية للتحدث الفعلي: كسر الجليد، مناظرات، تمثيل أدوار، وجلسات أفلام وموسيقى بإشراف معلمينا. انضم في أي أسبوع وبأي مستوى وابنِ ثقة حقيقية."),
 ("English Grammar Masterclass","دورة القواعد الإنجليزية المتقدمة",
  "The complete grammar map — from tenses to reported speech.",
  "خريطة القواعد الكاملة — من الأزمنة إلى الكلام المنقول.",
  "Six weeks to organised, confident grammar: the full tense system, articles, modals, complex clauses, passive and reported speech — closed by a clinic on the errors Arabic speakers make most.",
  "ستة أسابيع نحو قواعد منظمة وواثقة: منظومة الأزمنة الكاملة، الأدوات، الأفعال الناقصة، الجمل المركبة، المبني للمجهول والكلام المنقول — وتُختتم بعيادة لأكثر أخطاء الناطقين بالعربية شيوعًا."),
 ("Academic Writing Skills","مهارات الكتابة الأكاديمية",
  "Essays, reports and research writing for university success.",
  "المقالات والتقارير والكتابة البحثية للنجاح الجامعي.",
  "From paragraph architecture to a polished final essay: thesis building, cohesion, summarising, paraphrasing and referencing — everything university study demands of your writing.",
  "من بناء الفقرة إلى مقال نهائي متقن: بناء الأطروحة، الترابط، التلخيص، إعادة الصياغة والتوثيق — كل ما تتطلبه الدراسة الجامعية من كتابتك."),
 ("English for Kids & Teens","الإنجليزية للأطفال والناشئين",
  "Fun classroom learning: phonics, stories, songs and games.",
  "تعلّم صفّي ممتع: صوتيات وقصص وأغانٍ وألعاب.",
  "A joyful classroom method for young learners: phonics, story time, songs, vocabulary games and confidence-building performances, in small groups by age.",
  "منهج صفّي مبهج للصغار: الصوتيات، وقت القصة، الأغاني، ألعاب المفردات وعروض تبني الثقة، في مجموعات صغيرة حسب العمر."),
 ("TESOL Teacher Training Certificate","شهادة تدريب معلمي الإنجليزية TESOL",
  "Become a certified English teacher in 10 weeks.",
  "كن معلمًا معتمدًا للغة الإنجليزية خلال 10 أسابيع.",
  "Professional TESOL certification: language-learning principles, lesson planning, classroom management, teaching the four skills and assessment — completed with observed teaching practice and a certificate.",
  "شهادة TESOL مهنية: مبادئ تعلم اللغة، تخطيط الدروس، إدارة الصف، تدريس المهارات الأربع والتقييم — وتُستكمل بتدريب عملي مُقيَّم وشهادة معتمدة."),
]
slugs = ["general-english-beginner-a1","general-english-elementary-a2","general-english-intermediate-b1",
         "general-english-upper-intermediate-b2","business-english-essentials","ielts-exam-preparation",
         "toefl-ibt-preparation","english-conversation-club","english-grammar-masterclass",
         "academic-writing-skills","english-for-kids-teens","tesol-teacher-training-certificate"]
vid = 0
for i,(ne,na,se,sa,de,da) in enumerate(V,1):
    for lang,name,short,desc,slug in (("en",ne,se,de,slugs[i-1]),("ar",na,sa,da,slugs[i-1]+"-ar")):
        vid += 1
        w(f"INSERT INTO product_variations (id,products_id,name,slug,short_description,description,langue,created_at,updated_at) VALUES "
          f"({vid},{i},{q(name)},{q(slug)},{q(short)},{q(desc)},{q(lang)},NOW(),NOW());")

# ---------------------------------------------------------------- lessons
L = {
 1:[("The English Alphabet & Sounds","الحروف والأصوات الإنجليزية"),("Greetings & Introductions","التحيات والتعارف"),
    ("Numbers, Dates & Time","الأرقام والتواريخ والوقت"),("Present Simple: verb 'to be'","المضارع البسيط: فعل الكينونة"),
    ("Family & Everyday Vocabulary","مفردات العائلة والحياة اليومية"),("Speaking Lab: First Conversations","مختبر المحادثة: أولى المحادثات")],
 2:[("Daily Routines & Present Simple","الروتين اليومي والمضارع البسيط"),("Past Simple: Regular & Irregular","الماضي البسيط: الأفعال المنتظمة والشاذة"),
    ("Shopping & Money Language","لغة التسوق والمال"),("Comparatives & Superlatives","المقارنة والتفضيل"),
    ("Travel & Directions","السفر والاتجاهات"),("Speaking Lab: Telling Stories","مختبر المحادثة: سرد القصص")],
 3:[("Present Perfect in Context","المضارع التام في السياق"),("Future Forms & Plans","صيغ المستقبل والخطط"),
    ("Opinion & Discussion Language","لغة الرأي والنقاش"),("Everyday Phrasal Verbs","الأفعال المركبة اليومية"),
    ("Listening: Real Conversations","الاستماع: محادثات واقعية"),("Speaking Lab: Debates","مختبر المحادثة: مناظرات")],
 4:[("Advanced Tense Review","مراجعة الأزمنة المتقدمة"),("Conditionals & Hypotheticals","الجمل الشرطية والافتراضية"),
    ("Idioms & Collocations","التعابير والمتلازمات اللفظية"),("Reading: Press & Media","القراءة: الصحافة والإعلام"),
    ("Writing: Structured Essays","الكتابة: مقالات منظمة"),("Speaking Lab: Presentations","مختبر المحادثة: عروض تقديمية")],
 5:[("Emails that Get Answers","رسائل بريد تحصل على ردود"),("Meetings & Negotiations","الاجتماعات والمفاوضات"),
    ("Presentations with Impact","عروض تقديمية مؤثرة"),("Phone & Video Calls","المكالمات الهاتفية والمرئية"),
    ("CVs & Interviews","السيرة الذاتية والمقابلات"),("Case Study: Close the Deal","دراسة حالة: إتمام الصفقة")],
 6:[("Listening Strategies","استراتيجيات الاستماع"),("Reading: Skimming & Scanning","القراءة السريعة والمسح"),
    ("Writing Task 1: Data & Letters","المهمة الكتابية 1: البيانات والرسائل"),("Writing Task 2: Essays","المهمة الكتابية 2: المقالات"),
    ("Speaking Parts 1–3","المحادثة: الأجزاء 1–3"),("Full Mock Test & Feedback","اختبار تجريبي كامل وتقييم")],
 7:[("TOEFL iBT Format Overview","نظرة عامة على صيغة الاختبار"),("Reading Section Mastery","إتقان قسم القراءة"),
    ("Listening: Lectures & Notes","الاستماع: محاضرات وتدوين ملاحظات"),("Speaking: Integrated Tasks","المحادثة: مهام متكاملة"),
    ("Writing: Integrated & Independent","الكتابة المتكاملة والمستقلة"),("Full Mock Test & Feedback","اختبار محاكاة كامل وتقييم")],
 8:[("Icebreakers & Small Talk","كسر الجليد والدردشة"),("Culture Corner","ركن الثقافة"),
    ("Debate Night","ليلة النقاش"),("Movie & Music English","الإنجليزية عبر الأفلام والموسيقى"),
    ("Role-Play Scenarios","سيناريوهات تمثيل الأدوار"),("Open Mic Session","جلسة المنصة الحرة")],
 9:[("Tenses: The Complete Map","خريطة الأزمنة الكاملة"),("Articles & Determiners","أدوات التعريف والتنكير"),
    ("Modals & Meaning","الأفعال الناقصة ومعانيها"),("Relative & Noun Clauses","الجمل الموصولة والاسمية"),
    ("Passive & Reported Speech","المبني للمجهول والكلام المنقول"),("Grammar Clinic: Common Errors","عيادة القواعد: الأخطاء الشائعة")],
 10:[("Paragraph Architecture","بناء الفقرة"),("Thesis & Argumentation","الأطروحة والحجاج"),
    ("Cohesion & Linking","الترابط وأدوات الربط"),("Summarising & Paraphrasing","التلخيص وإعادة الصياغة"),
    ("Referencing Basics","أساسيات التوثيق"),("Portfolio: Final Essay","ملف الأعمال: المقال النهائي")],
 11:[("Phonics & First Words","الصوتيات والكلمات الأولى"),("Story Time","وقت القصة"),
    ("Songs & Chants","الأغاني والأناشيد"),("Games & Vocabulary","الألعاب والمفردات"),
    ("Show & Tell","اعرض وتحدث"),("Mini Performance Day","يوم العرض الصغير")],
 12:[("Principles of Language Learning","مبادئ تعلم اللغة"),("Lesson Planning","تخطيط الدروس"),
    ("Classroom Management","إدارة الصف"),("Teaching the Four Skills","تدريس المهارات الأربع"),
    ("Assessment & Feedback","التقييم والتغذية الراجعة"),("Observed Teaching Practice","التدريب العملي المُقيَّم")],
}
sid = 0
for pid, lessons in L.items():
    for order,(en,ar) in enumerate(lessons,1):
        sid += 1
        w(f"INSERT INTO product_studies (id,products_id,name_ar,name_en,resource_id,`order`,opens_after_purchase_days,created_at,updated_at) VALUES "
          f"({sid},{pid},{q(ar)},{q(en)},NULL,{order},0,NOW(),NOW());")

# ---------------------------------------------------------------- quizzes
QZ = [
 ("Grammar Basics — A1","أساسيات القواعد — A1",2,1,[
   ("Choose the correct form: She ___ a teacher.","اختر الصيغة الصحيحة: She ___ a teacher.",
    [("is","is",1),("are","are",0),("am","am",0),("be","be",0)]),
   ("What is the plural of 'child'?","ما جمع كلمة 'child'؟",
    [("children","children",1),("childs","childs",0),("childes","childes",0),("child","child",0)]),
   ("Select the correct article: ___ apple.","اختر الأداة الصحيحة: ___ apple.",
    [("an","an",1),("a","a",0),("the","the",0),("no article","بدون أداة",0)]),
   ("Which word is a verb?","أي كلمة من التالية فعل؟",
    [("run","run",1),("happy","happy",0),("blue","blue",0),("table","table",0)]),
   ("Complete: They ___ from Egypt.","أكمل: They ___ from Egypt.",
    [("are","are",1),("is","is",0),("am","am",0),("be","be",0)]),
 ]),
 ("Vocabulary Check — B1","فحص المفردات — B1",2,3,[
   ("'Postpone' most nearly means…","كلمة 'postpone' تعني تقريبًا…",
    [("delay","تأجيل (delay)",1),("cancel","إلغاء (cancel)",0),("finish","إنهاء (finish)",0),("begin","بدء (begin)",0)]),
   ("A person who writes news articles is a…","الشخص الذي يكتب المقالات الإخبارية هو…",
    [("journalist","صحفي (journalist)",1),("librarian","أمين مكتبة (librarian)",0),("accountant","محاسب (accountant)",0),("surgeon","جرّاح (surgeon)",0)]),
   ("Choose the correct collocation: ___ a decision.","اختر المتلازمة الصحيحة: ___ a decision.",
    [("make","make",1),("do","do",0),("say","say",0),("put","put",0)]),
   ("'Reliable' describes someone who is…","كلمة 'reliable' تصف شخصًا…",
    [("trustworthy","جديرًا بالثقة (trustworthy)",1),("lazy","كسولًا (lazy)",0),("funny","مضحكًا (funny)",0),("tall","طويلًا (tall)",0)]),
   ("The opposite of 'ancient' is…","عكس كلمة 'ancient' هو…",
    [("modern","حديث (modern)",1),("old","قديم (old)",0),("historic","تاريخي (historic)",0),("antique","أثري (antique)",0)]),
 ]),
 ("IELTS Reading Mini-Test","اختبار قراءة آيلتس مصغّر",1,6,[
   ("Skimming a text means reading…","القراءة السريعة (skimming) تعني قراءة النص…",
    [("quickly for the main idea","بسرعة لالتقاط الفكرة الرئيسية",1),("every word carefully","كل كلمة بعناية",0),
     ("only the title","العنوان فقط",0),("the conclusion twice","الخاتمة مرتين",0)]),
   ("'True / False / Not Given' answers must be based on…","إجابات 'صحيح / خطأ / غير مذكور' يجب أن تعتمد على…",
    [("the passage only","النص فقط",1),("your own knowledge","معلوماتك الشخصية",0),("the title","العنوان",0),("grammar rules","قواعد اللغة",0)]),
   ("The best strategy for matching headings is to…","أفضل استراتيجية لمطابقة العناوين هي…",
    [("identify each paragraph's main idea","تحديد الفكرة الرئيسية لكل فقرة",1),("read the first word only","قراءة أول كلمة فقط",0),
     ("count the sentences","عدّ الجمل",0),("translate every word","ترجمة كل كلمة",0)]),
   ("In sentence completion, answers must…","في إكمال الجمل، يجب أن تكون الإجابات…",
    [("respect the word limit","ضمن حد الكلمات المسموح",1),("be full sentences","جملًا كاملة",0),
     ("be synonyms only","مرادفات فقط",0),("be capitalised","بأحرف كبيرة",0)]),
   ("'Not Given' means the information is…","'غير مذكور' تعني أن المعلومة…",
    [("absent from the passage","غير موجودة في النص",1),("false","خاطئة",0),("true","صحيحة",0),("implied","ضمنية",0)]),
 ]),
]
qid = 0; quid = 0; rid = 0
for qz_i,(ne,na,type_id,pid,questions) in enumerate(QZ,1):
    w(f"INSERT INTO quizzes (id,name_ar,name_en,score,max_attempts,type_id,created_at,updated_at) VALUES "
      f"({qz_i},{q(na)},{q(ne)},60,3,{type_id},NOW(),NOW());")
    w(f"INSERT INTO quiz_products (product_id,quiz_id,nb_question_affiche,score_success,use_own_questions,opens_after_purchase_days,created_at,updated_at) VALUES "
      f"({pid},{qz_i},5,60,0,0,NOW(),NOW());")
    for (qe,qa,answers) in questions:
        quid += 1
        w(f"INSERT INTO questions (id,name_ar,name_en,point,quiz_id,created_at,updated_at) VALUES "
          f"({quid},{q(qa)},{q(qe)},20,{qz_i},NOW(),NOW());")
        for (ae,aa,ok) in answers:
            rid += 1
            w(f"INSERT INTO reponse_questions (id,titre_ar,titre_en,question_id,is_correcte,created_at,updated_at) VALUES "
              f"({rid},{q(aa)},{q(ae)},{quid},{ok},NOW(),NOW());")

# ---------------------------------------------------------------- books
B = [
 ("English Grammar in Practice","القواعد الإنجليزية بالتطبيق","book-grammar.svg",12,
  "Clear rules, hundreds of examples and self-check exercises.","قواعد واضحة ومئات الأمثلة وتمارين ذاتية التصحيح."),
 ("IELTS Vocabulary Builder","بناء مفردات الآيلتس","book-ielts-vocab.svg",15,
  "Topic-by-topic academic vocabulary for Band 7+.","مفردات أكاديمية موضوعًا بموضوع للمستوى +7."),
 ("Business English Handbook","دليل الإنجليزية للأعمال","book-business.svg",15,
  "Templates and phrases for emails, meetings and negotiations.","قوالب وعبارات للرسائل والاجتماعات والمفاوضات."),
 ("Academic Writing Guide","دليل الكتابة الأكاديمية","book-writing.svg",12,
  "From paragraph to research paper, step by step.","من الفقرة إلى الورقة البحثية خطوة بخطوة."),
 ("Phrasal Verbs Made Easy","الأفعال المركبة ببساطة","book-phrasal.svg",10,
  "The 300 most useful phrasal verbs with stories and quizzes.","أهم 300 فعل مركّب مع قصص واختبارات."),
 ("TOEFL Practice Tests","اختبارات توفل تجريبية","book-toefl.svg",15,
  "Four full-length simulated TOEFL iBT tests with keys.","أربعة اختبارات محاكاة كاملة مع مفاتيح الإجابة."),
]
for i,(te,ta,img,prix,se,sa) in enumerate(B,1):
    w(f"INSERT INTO books (id,titre_ar,titre_en,description_short_ar,description_short_en,description_ar,description_en,file,image,prix,created_at,updated_at) VALUES "
      f"({i},{q(ta)},{q(te)},{q(sa)},{q(se)},{q(sa)},{q(se)},'demo-sample.pdf',{q(img)},{prix}.00,NOW(),NOW());")

# ---------------------------------------------------------------- packages
PK = [
 ("Complete English Mastery 2026","الإتقان الكامل للإنجليزية 2026","pack-mastery.svg",
  "All four general English levels (A1→B2) at 20% off.","المستويات الأربعة للإنجليزية العامة (A1→B2) بخصم 20٪.",[1,2,3,4],20),
 ("Exam Success Bundle","حزمة النجاح في الاختبارات","pack-exams.svg",
  "IELTS + TOEFL + Academic Writing, together at 15% off.","آيلتس + توفل + الكتابة الأكاديمية معًا بخصم 15٪.",[6,7,10],15),
 ("Professional English Pack","حزمة الإنجليزية المهنية","pack-business.svg",
  "Business English, Grammar Masterclass and TESOL at 15% off.","إنجليزية الأعمال ودورة القواعد وTESOL بخصم 15٪.",[5,9,12],15),
]
for i,(te,ta,img,de,da,prods,disc) in enumerate(PK,1):
    w(f"INSERT INTO packages (id,title,title_ar,image,description,description_ar,is_active,created_at,updated_at) VALUES "
      f"({i},{q(te)},{q(ta)},{q(img)},{q(de)},{q(da)},1,NOW(),NOW());")
    for pid in prods:
        w(f"INSERT INTO package_products (package_id,product_id,valeur_reduction,discount_type,is_active,created_at,updated_at) VALUES "
          f"({i},{pid},{disc}.00,'percentage',1,NOW(),NOW());")

# ---------------------------------------------------------------- testimonials
AV = [
 ("Rana M.","s1.svg",
  "The IELTS course took me from Band 5.5 to 7.5 in two months. The mock tests felt exactly like the real exam.",
  "نقلتني دورة الآيلتس من 5.5 إلى 7.5 خلال شهرين. الاختبارات التجريبية كانت مطابقة تمامًا للاختبار الحقيقي."),
 ("Lina K.","s2.svg",
  "Business English changed how I write at work — my emails finally get answers. Best career investment I've made.",
  "غيّرت دورة إنجليزية الأعمال طريقة كتابتي في العمل — رسائلي أخيرًا تحصل على ردود. أفضل استثمار مهني قمت به."),
 ("Ahmed H.","s3.svg",
  "I started at A1 knowing almost nothing. One year later I passed B2 and got a scholarship interview — in English.",
  "بدأت من مستوى A1 دون معرفة تُذكر. بعد عام اجتزت B2 وأجريت مقابلة منحة دراسية — بالإنجليزية."),
 ("Sami F.","s4.svg",
  "The Conversation Club is gold. Free, fun, and the teachers actually correct you kindly. My fluency doubled.",
  "نادي المحادثة كنز حقيقي: مجاني وممتع، والمعلمون يصححون لك بلطف. تضاعفت طلاقتي."),
 ("Nour T.","s5.svg",
  "TESOL certification here got me my first teaching job within a month of graduating. The observed practice is priceless.",
  "شهادة TESOL من هنا أوصلتني إلى أول وظيفة تدريس خلال شهر من التخرج. التدريب العملي المُقيَّم لا يُقدّر بثمن."),
 ("Youssef B.","s6.svg",
  "My kids beg to go to English class now. Songs, games, stories — they learn without noticing they're learning.",
  "أطفالي يتوسلون للذهاب إلى حصة الإنجليزية الآن. أغانٍ وألعاب وقصص — يتعلمون دون أن يشعروا أنهم يتعلمون."),
]
for i,(client,img,en,ar) in enumerate(AV,1):
    w(f"INSERT INTO avis_acceuil (id,client,image,avis_en,avis_ar,is_active,`order`,created_at,updated_at) VALUES "
      f"({i},{q(client)},{q(img)},{q(en)},{q(ar)},1,{i},NOW(),NOW());")

# ---------------------------------------------------------------- carousels + featured + partners
CR = [
 ("Master English. Open Your World.","أتقن الإنجليزية وافتح عالمك.",
  "Live classes, certified teachers and structured paths from A1 to IELTS Band 7+.",
  "حصص مباشرة ومعلمون معتمدون ومسارات منظمة من A1 حتى آيلتس +7.","hero-1.svg"),
 ("Learn from Certified Teachers","تعلّم مع معلمين معتمدين",
  "Small groups, real speaking practice and feedback that moves you forward every single week.",
  "مجموعات صغيرة وتدريب حقيقي على المحادثة وتقييم يدفعك للأمام كل أسبوع.","hero-2.svg"),
]
for i,(se,sa,de,da,img) in enumerate(CR,1):
    w(f"INSERT INTO carousels (id,slug_ar,slug_en,description_ar,description_en,image,is_active,`order`,created_at,updated_at) VALUES "
      f"({i},{q(sa)},{q(se)},{q(da)},{q(de)},{q(img)},1,{i},NOW(),NOW());")

for order,pid in enumerate([1,3,5,6,8,12],1):
    w(f"INSERT INTO products_acceuil (product_id,is_active,`order`,created_at,updated_at) VALUES ({pid},1,{order},NOW(),NOW());")

for i,name in enumerate(["Cambridge Path","LinguaCert","EduBridge","GlobalSpeak"],1):
    w(f"INSERT INTO nos_partenaires (id,nom,url,logo,`order`,is_active,created_at,updated_at) VALUES "
      f"({i},{q(name)},'#','partners/p{i}.svg',{i},1,NOW(),NOW());")

# ---------------------------------------------------------------- ratings
RT = [
 (1,5,"Perfect for absolute beginners — patient teacher, clear lessons."),
 (1,5,"I can finally introduce myself and hold a small conversation!"),
 (3,4,"Great debates and listening material. I wish it were longer."),
 (3,5,"The phrasal verbs lesson alone was worth the price."),
 (5,5,"My work emails improved within two weeks. Highly practical."),
 (6,5,"Got Band 7.0 — the writing frameworks are exceptional."),
 (6,4,"Intense but effective. Mock test feedback was very detailed."),
 (12,5,"The observed teaching practice made me a real teacher."),
]
for pid,rating,txt in RT:
    w(f"INSERT INTO course_ratings (product_id,student_id,rating,commentaire,is_approved,approved_at,created_at,updated_at) VALUES "
      f"({pid},NULL,{rating},{q(txt)},1,NOW(),NOW(),NOW());")

# ---------------------------------------------------------------- sujets (speaking exam topics)
SJ = [
 ("fa","en","Speaking exam — Foundation: introduce yourself, your family and your daily routine. Describe a typical day using the present simple, then answer three follow-up questions from the examiner."),
 ("fa","ar","اختبار المحادثة — المستوى التأسيسي: قدّم نفسك وعائلتك وروتينك اليومي. صف يومًا اعتياديًا باستخدام المضارع البسيط، ثم أجب عن ثلاثة أسئلة متابعة من الممتحن."),
 ("fi","en","Speaking exam — Fluency: narrate a past experience that changed you, compare two cities you know well, and give your opinion on technology in education with two supporting reasons."),
 ("fi","ar","اختبار المحادثة — مستوى الطلاقة: اسرد تجربة سابقة غيّرتك، وقارن بين مدينتين تعرفهما جيدًا، وأبدِ رأيك في التكنولوجيا في التعليم مع سببين داعمين."),
 ("pt","en","Speaking exam — Proficiency: discuss the impact of globalisation on local cultures. Structure a two-minute argument with an introduction, two supported points and a conclusion."),
 ("pt","ar","اختبار المحادثة — مستوى الإتقان: ناقش أثر العولمة على الثقافات المحلية. قدّم حجة لمدة دقيقتين ببنية واضحة: مقدمة ونقطتان مدعومتان وخاتمة."),
]
for typ,lang,desc in SJ:
    w(f"INSERT INTO sujets (description,lang,type,created_at,updated_at) VALUES ({q(desc)},{q(lang)},{q(typ)},NOW(),NOW());")

# ---------------------------------------------------------------- course sessions (IELTS demo)
w("INSERT INTO course_sessions (product_id,title,description,session_date,start_time,end_time,session_type,instructor_name,status,created_at,updated_at) VALUES "
  "(6,'IELTS Live — Writing Task 2 Workshop','Argument essays: structure, cohesion and band descriptors.',DATE_ADD(CURDATE(), INTERVAL 7 DAY),'18:00:00','20:00:00','online','Sarah Mitchell','scheduled',NOW(),NOW());")
w("INSERT INTO course_sessions (product_id,title,description,session_date,start_time,end_time,session_type,instructor_name,status,created_at,updated_at) VALUES "
  "(6,'IELTS Live — Speaking Mock Interviews','One-to-one mock speaking tests with examiner feedback.',DATE_ADD(CURDATE(), INTERVAL 14 DAY),'18:00:00','20:00:00','online','James Carter','scheduled',NOW(),NOW());")

# ---------------------------------------------------------------- blog & articles
w(f"INSERT INTO blogs (id,titre_ar,titre_en,meta_title_ar,meta_title_en,description_ar,description_en,description_short_ar,description_short_en,slug,image,author_ar,author_en,published_date,is_active,views_count,created_at,updated_at) VALUES "
  f"(1,{q('خمس عادات لمتعلمي اللغات الناجحين')},{q('5 Habits of Highly Successful Language Learners')},"
  f"{q('عادات المتعلمين الناجحين')},{q('Successful Learner Habits')},"
  f"{q('من التعرض اليومي للغة إلى التحدث دون خوف من الخطأ — خمس عادات نلاحظها لدى أنجح طلابنا، وكيف تبنيها خطوة بخطوة.')},"
  f"{q('From daily exposure to fearless speaking — five habits we see in our most successful students, and how to build each one step by step.')},"
  f"{q('خمس عادات تصنع الفرق في رحلة تعلمك.')},{q('Five habits that transform your learning journey.')},"
  f"'5-habits-successful-language-learners','blog-1.svg',{q('فريق أب سكيلز')},{q('Upskills Team')},CURDATE(),1,0,NOW(),NOW());")
ARTS = [
 ("كيف تبدأ التفكير بالإنجليزية","How to Start Thinking in English",
  "توقف عن الترجمة في رأسك: تمارين عملية تدرّب دماغك على التفكير مباشرة بالإنجليزية.",
  "Stop translating in your head: practical exercises that train your brain to think directly in English.",
  "how-to-start-thinking-in-english","article-1.svg"),
 ("دليل كامل لكتابة المهمة الثانية في الآيلتس","IELTS Writing Task 2: A Complete Guide",
  "بنية المقال، إدارة الوقت، ومعايير التقييم — كل ما تحتاجه لمهمة الكتابة الثانية.",
  "Essay structure, time management and band descriptors — everything you need for Task 2.",
  "ielts-writing-task-2-complete-guide","article-2.svg"),
]
for i,(ta,te,da,de,slug,img) in enumerate(ARTS,1):
    w(f"INSERT INTO articles (id,titre_ar,titre_en,meta_title_ar,meta_title_en,description_ar,description_en,description_short_ar,description_short_en,slug,image,author_ar,author_en,published_date,is_active,views_count,created_at,updated_at) VALUES "
      f"({i},{q(ta)},{q(te)},{q(ta)},{q(te)},{q(da)},{q(de)},{q(da)},{q(de)},{q(slug)},{q(img)},{q('فريق أب سكيلز')},{q('Upskills Team')},CURDATE(),1,0,NOW(),NOW());")

# ---------------------------------------------------------------- site info + rebrand strings in CMS
w("UPDATE information SET email='support@upskillsacademy.com', phone='+44 20 7946 0958', "
  "adresse='12 Learning Street, London, United Kingdom', "
  "facebook='https://facebook.com/upskillsacademy', youtube='https://youtube.com/@upskillsacademy', "
  "instagram='https://instagram.com/upskillsacademy';")

# Brand string replacements in pages + email_templates (EN then AR)
REPL = [
 ("Swedish Academy of Sport Training","Upskills Academy"),
 ("Swedish Academy for Sports Training","Upskills Academy"),
 ("Swedish Academy","Upskills Academy"),
 ("Swedish University","Upskills Academy"),
 ("sport training","English teaching"),("Sport Training","English Teaching"),
 ("sports training","English teaching"),("Sports Training","English Teaching"),
 ("sports science","language education"),("Sports Science","Language Education"),
 ("sport science","language education"),
 ("sports coaching","English teaching"),("Sports Coaching","English Teaching"),
 ("swedish-academy.se","upskillsacademy.com"),
 ("الأكاديمية السويدية للتدريب الرياضي","أكاديمية أب سكيلز لتعليم الإنجليزية"),
 ("الأكاديمية السويدية للعلوم الرياضية","أكاديمية أب سكيلز لتعليم الإنجليزية"),
 ("الأكاديمية السويدية","أكاديمية أب سكيلز"),
 ("التدريب الرياضي","تعليم اللغة الإنجليزية"),
 ("العلوم الرياضية","تعليم اللغات"),
 ("الرياضية","التعليمية"),("الرياضي","التعليمي"),
]
for col_table, cols in [("pages",["titre_ar","titre_en","meta_title_ar","meta_title_en","description_ar","description_en"]),
                        ("email_templates",["name","subject","content","text_content","description"])]:
    for old,new in REPL:
        sets = ", ".join([f"`{c}`=REPLACE(COALESCE(`{c}`,''),{q(old)},{q(new)})" for c in cols])
        w(f"UPDATE {col_table} SET {sets};")

w("SET FOREIGN_KEY_CHECKS=1;")
w("COMMIT;")

sql = "\n".join(out) + "\n"
with open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "seed_upskills_demo.sql"),"w",encoding="utf-8") as f:
    f.write(sql)
print(f"wrote seed_upskills_demo.sql — {len(out)} statements, {len(sql)//1024} KB")
