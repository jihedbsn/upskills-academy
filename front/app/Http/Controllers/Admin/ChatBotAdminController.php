<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatBotResponse;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class ChatBotAdminController extends Controller
{
    /**
     * Afficher le tableau de bord du chatbot
     */
    public function index()
    {
        $stats = [
            'total_responses' => ChatBotResponse::count(),
            'active_responses' => ChatBotResponse::where('is_active', true)->count(),
            'total_conversations' => ChatConversation::count(),
            'active_conversations' => ChatConversation::where('status', 'active')->count(),
            'total_messages' => ChatMessage::count(),
            'recent_conversations' => ChatConversation::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return view('admin.chatbot.index', compact('stats'));
    }

    /**
     * Afficher la liste des réponses
     */
    public function responses()
    {
        $responses = ChatBotResponse::orderBy('priority', 'desc')
            ->orderBy('category')
            ->paginate(20);

        return view('admin.chatbot.responses', compact('responses'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = [
            'greeting' => 'Salutation',
            'about' => 'À propos',
            'courses' => 'Cours',
            'pricing' => 'Prix',
            'registration' => 'Inscription',
            'schedule' => 'Horaires',
            'certification' => 'Certification',
            'contact' => 'Contact',
            'help' => 'Aide',
            'farewell' => 'Au revoir',
            'faq' => 'FAQ',
            'general' => 'Général'
        ];

        return view('admin.chatbot.create', compact('categories'));
    }

    /**
     * Sauvegarder une nouvelle réponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'nullable|string|max:255',
            'question_en' => 'required|string',
            'question_ar' => 'required|string',
            'response_en' => 'required|string',
            'response_ar' => 'required|string',
            'category' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        ChatBotResponse::create([
            'keyword' => $request->keyword,
            'question_en' => $request->question_en,
            'question_ar' => $request->question_ar,
            'response_en' => $request->response_en,
            'response_ar' => $request->response_ar,
            'category' => $request->category,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.chatbot.responses')
            ->with('success', 'Réponse créée avec succès');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(ChatBotResponse $response)
    {
        $categories = [
            'greeting' => 'Salutation',
            'about' => 'À propos',
            'courses' => 'Cours',
            'pricing' => 'Prix',
            'registration' => 'Inscription',
            'schedule' => 'Horaires',
            'certification' => 'Certification',
            'contact' => 'Contact',
            'help' => 'Aide',
            'farewell' => 'Au revoir',
            'faq' => 'FAQ',
            'general' => 'Général'
        ];

        return view('admin.chatbot.edit', compact('response', 'categories'));
    }

    /**
     * Mettre à jour une réponse
     */
    public function update(Request $request, ChatBotResponse $response)
    {
        $request->validate([
            'keyword' => 'nullable|string|max:255',
            'question_en' => 'required|string',
            'question_ar' => 'required|string',
            'response_en' => 'required|string',
            'response_ar' => 'required|string',
            'category' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $response->update([
            'keyword' => $request->keyword,
            'question_en' => $request->question_en,
            'question_ar' => $request->question_ar,
            'response_en' => $request->response_en,
            'response_ar' => $request->response_ar,
            'category' => $request->category,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.chatbot.responses')
            ->with('success', 'Réponse mise à jour avec succès');
    }

    /**
     * Supprimer une réponse
     */
    public function destroy(ChatBotResponse $response)
    {
        $response->delete();

        return redirect()->route('admin.chatbot.responses')
            ->with('success', 'Réponse supprimée avec succès');
    }

    /**
     * Afficher les conversations
     */
    public function conversations()
    {
        $conversations = ChatConversation::with('user', 'messages')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.chatbot.conversations', compact('conversations'));
    }

    /**
     * Afficher une conversation spécifique
     */
    public function showConversation(ChatConversation $conversation)
    {
        $messages = $conversation->messages()->orderBy('sent_at', 'asc')->get();

        return view('admin.chatbot.conversation', compact('conversation', 'messages'));
    }

    /**
     * Obtenir les statistiques en AJAX
     */
    public function getStats()
    {
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'active_conversations' => ChatConversation::where('status', 'active')->count(),
            'total_messages' => ChatMessage::count(),
            'user_messages' => ChatMessage::where('sender_type', 'user')->count(),
            'bot_messages' => ChatMessage::where('sender_type', 'bot')->count(),
            'recent_conversations' => ChatConversation::where('created_at', '>=', now()->subDays(7))->count()
        ];

        return response()->json($stats);
    }
}
