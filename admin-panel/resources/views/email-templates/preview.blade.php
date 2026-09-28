<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu Email - {{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .preview-header {
            background: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .preview-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .preview-info {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .preview-info strong {
            color: #495057;
        }
        .email-content {
            padding: 20px;
            min-height: 400px;
        }
        .preview-actions {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .alert-info {
            background: #e7f3ff;
            border-color: #2563eb;
            color: #1e40af;
        }
        .device-preview {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .device {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .device-header {
            background: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            color: #666;
        }
        .device-content {
            height: 400px;
            overflow-y: auto;
        }
        .device-content iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        @media (max-width: 768px) {
            .device-preview {
                flex-direction: column;
            }
            .preview-container {
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>📧 Aperçu Email Template</h1>
            <p>Prévisualisation avec des données d'exemple</p>
        </div>

        <div class="preview-info">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong>Sujet:</strong> {{ $subject }}
                </div>
                <div>
                    <strong>Date:</strong> {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>ℹ️ Information:</strong> Ceci est un aperçu avec des données d'exemple. Les variables réelles seront remplacées lors de l'envoi.
        </div>

        <div class="device-preview">
            <div class="device">
                <div class="device-header">
                    🖥️ Version Desktop
                </div>
                <div class="device-content">
                    <iframe srcdoc="{{ htmlspecialchars($content) }}"></iframe>
                </div>
            </div>
            
            <div class="device" style="max-width: 375px;">
                <div class="device-header">
                    📱 Version Mobile
                </div>
                <div class="device-content">
                    <iframe srcdoc="{{ htmlspecialchars($content) }}"></iframe>
                </div>
            </div>
        </div>

        <div class="preview-actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Imprimer
            </button>
            <button onclick="copyEmailContent()" class="btn btn-secondary">
                📋 Copier HTML
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                ❌ Fermer
            </button>
        </div>
    </div>

    <script>
        function copyEmailContent() {
            const content = `{!! addslashes($content) !!}`;
            navigator.clipboard.writeText(content).then(function() {
                alert('✅ Contenu HTML copié dans le presse-papiers!');
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                // Fallback pour les navigateurs plus anciens
                const textArea = document.createElement('textarea');
                textArea.value = content;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('✅ Contenu HTML copié dans le presse-papiers!');
            });
        }

        // Auto-resize iframes
        window.addEventListener('load', function() {
            const iframes = document.querySelectorAll('iframe');
            iframes.forEach(function(iframe) {
                iframe.onload = function() {
                    try {
                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                        const height = Math.max(
                            doc.body.scrollHeight,
                            doc.body.offsetHeight,
                            doc.documentElement.clientHeight,
                            doc.documentElement.scrollHeight,
                            doc.documentElement.offsetHeight
                        );
                        iframe.style.height = height + 'px';
                    } catch (e) {
                        // Cross-origin restrictions
                        console.log('Cannot access iframe content due to cross-origin restrictions');
                    }
                };
            });
        });
    </script>
</body>
</html>