<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat - {{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</title>
    <meta name="description" content="Certificat de réussite pour {{ $certificate->student->first_name }} {{ $certificate->student->last_name }} - {{ $certificate->product->variation_title }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .certificate-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 1200px;
            overflow: hidden;
        }

        .certificate-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .certificate-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .certificate-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .certificate-content {
            padding: 2rem;
        }

        .certificate-image {
            text-align: center;
            margin-bottom: 2rem;
        }

        .certificate-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .certificate-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #495057;
        }

        .detail-value {
            color: #212529;
        }

        .verification-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .verification-info h5 {
            color: #1976d2;
            margin-bottom: 0.5rem;
        }

        .verification-info p {
            margin: 0;
            color: #424242;
        }

        .actions {
            text-align: center;
            padding: 1rem 0;
        }

        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .footer {
            background: #f8f9fa;
            padding: 1rem;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 768px) {
            .certificate-header h1 {
                font-size: 2rem;
            }

            .certificate-header p {
                font-size: 1rem;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-value {
                margin-top: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-container">
            <!-- Header -->
            <div class="certificate-header">
                <h1><i class="fas fa-certificate"></i> Certificat de Réussite</h1>
                <p>Upskills Academy</p>
            </div>

            <!-- Content -->
            <div class="certificate-content">
                <!-- Certificate Image -->
                <div class="certificate-image">
                    @if($certificate->file_path && file_exists(public_path($certificate->file_path)))
                        <img src="{{ asset($certificate->file_path) }}" alt="Certificat de {{ $certificate->student->first_name }} {{ $certificate->student->last_name }}">
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            L'image du certificat n'est pas disponible.
                        </div>
                    @endif
                </div>

                <!-- Certificate Details -->
                <div class="certificate-details">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user"></i> Étudiant:</span>
                        <span class="detail-value">{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-graduation-cap"></i> Cours:</span>
                        <span class="detail-value">{{ $certificate->product->variation_title }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-hashtag"></i> Numéro de série:</span>
                        <span class="detail-value"><strong>{{ $certificate->serial_number }}</strong></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar"></i> Date de génération:</span>
                        <span class="detail-value">{{ $certificate->generated_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-check-circle"></i> Statut:</span>
                        <span class="detail-value">
                            @if($certificate->is_valid)
                                <span class="badge bg-success">Valide</span>
                            @else
                                <span class="badge bg-danger">Invalide</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="verification-info">
                    <h5><i class="fas fa-shield-alt"></i> Vérification du Certificat</h5>
                    <p>Ce certificat a été généré automatiquement par le système Upskills Academy.
                    Le numéro de série <strong>{{ $certificate->serial_number }}</strong> permet de vérifier l'authenticité de ce certificat.</p>
                </div>

                <!-- Actions -->
                <div class="actions">
                    @if($certificate->file_path && file_exists(public_path($certificate->file_path)))
                        <a href="{{ asset($certificate->file_path) }}" download="certificate_{{ $certificate->serial_number }}.png" class="btn-download">
                            <i class="fas fa-download"></i> Télécharger le Certificat
                        </a>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><i class="fas fa-globe"></i> Upskills Academy - <a href="{{ env('APP_URL') }}" target="_blank">{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}</a></p>
                <p><small>Certificat généré le {{ $certificate->generated_at->format('d/m/Y à H:i') }}</small></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
