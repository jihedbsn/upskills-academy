<?php

namespace App\Helpers;

class PasswordGenerator
{
    /**
     * Générer un mot de passe aléatoire sécurisé
     *
     * @param int $length Longueur du mot de passe (défaut: 12)
     * @param bool $includeSymbols Inclure des symboles spéciaux
     * @return string
     */
    public static function generate(int $length = 12, bool $includeSymbols = true): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        // Construire le jeu de caractères
        $characters = $lowercase . $uppercase . $numbers;
        if ($includeSymbols) {
            $characters .= $symbols;
        }
        
        // S'assurer d'avoir au moins un caractère de chaque type
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)]; // Au moins une minuscule
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)]; // Au moins une majuscule
        $password .= $numbers[random_int(0, strlen($numbers) - 1)]; // Au moins un chiffre
        
        if ($includeSymbols) {
            $password .= $symbols[random_int(0, strlen($symbols) - 1)]; // Au moins un symbole
        }
        
        // Compléter avec des caractères aléatoires
        $remainingLength = $length - strlen($password);
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        // Mélanger les caractères pour éviter un pattern prévisible
        return str_shuffle($password);
    }
    
    /**
     * Générer un mot de passe simple sans symboles spéciaux
     * Plus facile à saisir pour les utilisateurs
     *
     * @param int $length
     * @return string
     */
    public static function generateSimple(int $length = 10): string
    {
        return self::generate($length, false);
    }
    
    /**
     * Générer un mot de passe avec des mots lisibles
     * Format: Mot1-Mot2-123
     *
     * @return string
     */
    public static function generateReadable(): string
    {
        $words = [
            'Apple', 'Beach', 'Cloud', 'Dream', 'Eagle', 'Forest', 'Garden', 'Happy',
            'Island', 'Jungle', 'Kitten', 'Light', 'Music', 'Nature', 'Ocean', 'Peace',
            'Quick', 'River', 'Sunny', 'Tiger', 'Unity', 'Valley', 'Water', 'Xray',
            'Yellow', 'Zebra', 'Bright', 'Calm', 'Dance', 'Energy', 'Fresh', 'Grace'
        ];
        
        $word1 = $words[array_rand($words)];
        $word2 = $words[array_rand($words)];
        $numbers = random_int(100, 999);
        
        return $word1 . '-' . $word2 . '-' . $numbers;
    }
    
    /**
     * Vérifier la force d'un mot de passe
     *
     * @param string $password
     * @return array
     */
    public static function checkStrength(string $password): array
    {
        $score = 0;
        $feedback = [];
        
        // Longueur
        if (strlen($password) >= 8) {
            $score += 2;
        } elseif (strlen($password) >= 6) {
            $score += 1;
            $feedback[] = 'Le mot de passe devrait faire au moins 8 caractères';
        } else {
            $feedback[] = 'Le mot de passe est trop court (minimum 6 caractères)';
        }
        
        // Minuscules
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Ajoutez des lettres minuscules';
        }
        
        // Majuscules
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Ajoutez des lettres majuscules';
        }
        
        // Chiffres
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Ajoutez des chiffres';
        }
        
        // Symboles
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 2;
        } else {
            $feedback[] = 'Ajoutez des caractères spéciaux (!@#$%^&*)';
        }
        
        // Déterminer le niveau
        if ($score >= 6) {
            $level = 'Fort';
        } elseif ($score >= 4) {
            $level = 'Moyen';
        } else {
            $level = 'Faible';
        }
        
        return [
            'score' => $score,
            'level' => $level,
            'feedback' => $feedback
        ];
    }
}
