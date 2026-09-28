<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsErrors
{
    use Importable;

    protected $importedStudents = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Ignorer les lignes vides
        if (empty($row['nom']) && empty($row['email']) && empty($row['telephone'])) {
            return null;
        }

        // Créer un nouvel utilisateur étudiant
        $user = new User([
            'name' => $row['nom'] ?? '', // Utiliser 'name' comme champ principal
            'nom' => $row['nom'] ?? '', // Champ spécifique pour le nom
            'email' => $row['email'] ?? '',
            'telephone' => $row['telephone'] ?? '',
            'role' => 'student', // Rôle par défaut
            'password' => bcrypt('password123'), // Mot de passe temporaire
        ]);

        // Stocker l'étudiant importé
        $this->importedStudents[] = [
            'nom' => $row['nom'] ?? '',
            'email' => $row['email'] ?? '',
            'telephone' => $row['telephone'] ?? '',
        ];

        return $user;
    }

    /**
     * Règles de validation pour l'importation
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function customValidationMessages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'email.unique' => 'Cet email existe déjà',
            'telephone.max' => 'Le téléphone ne peut pas dépasser 20 caractères',
        ];
    }

    /**
     * Gérer les erreurs lors de l'importation
     */
    public function onError(\Throwable $e)
    {
        // Log l'erreur ou la traiter selon vos besoins
        \Log::error('Erreur lors de l\'importation d\'un étudiant: ' . $e->getMessage());
    }

    /**
     * Récupérer les étudiants importés
     */
    public function getImportedStudents()
    {
        return $this->importedStudents;
    }
}
