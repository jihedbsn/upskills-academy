<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $study->name }} - PDF Viewer</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .pdf-header {
            background: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .pdf-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }

        .pdf-header .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .pdf-container {
            height: calc(100vh - 60px);
            width: 100%;
        }

        .pdf-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            font-size: 16px;
            color: #666;
        }

        @media (max-width: 768px) {
            .pdf-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .pdf-header h1 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <h1>{{ $study->name }}</h1>
        <div class="actions">
            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $study->product->id]) }}" class="btn btn-secondary">
                {{ __('messages.back_to_course') }}
            </a>
            <a href="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}"
               class="btn btn-primary"
               target="_blank">
                {{ __('messages.download') }}
            </a>
        </div>
    </div>

    <div class="pdf-container">
        <iframe
            src="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}"
            class="pdf-iframe"
            onload="hideLoading()">
            <div class="loading">
                {{ __('messages.loading_pdf') }}...
            </div>
        </iframe>
    </div>

    <script>
        function hideLoading() {
            // Le PDF est chargé
            console.log('PDF loaded successfully');
        }

        // Gestion des erreurs de chargement
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'IFRAME') {
                e.target.style.display = 'none';
                document.querySelector('.pdf-container').innerHTML =
                    '<div class="loading">' +
                    '<p>{{ __("messages.pdf_load_error") }}</p>' +
                    '<a href="{{ env("FILE_URL") }}uploads/resources/{{ $study->resource->localized_file }}" ' +
                    'class="btn btn-primary" target="_blank">' +
                    '{{ __("messages.download_pdf") }}</a>' +
                    '</div>';
            }
        });
    </script>
</body>
</html>
