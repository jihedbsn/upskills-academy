@extends('layouts.app')

@section('title', 'Test Chatbot')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Test du Chatbot Upskills Academy</h3>
                </div>
                <div class="card-body">
                    <p>Cette page teste l'affichage du widget du chatbot.</p>
                    <p>Vous devriez voir un bouton de chat flottant en bas à droite de l'écran.</p>
                    
                    <div class="alert alert-info">
                        <h5>Instructions :</h5>
                        <ul>
                            <li>Regardez en bas à droite de l'écran</li>
                            <li>Vous devriez voir un bouton bleu avec une icône de chat</li>
                            <li>Cliquez dessus pour ouvrir le chatbot</li>
                            <li>Testez les questions rapides</li>
                        </ul>
                    </div>
                    
                    <div class="text-center">
                        <a href="/chatbot" class="btn btn-primary">Aller à l'interface complète du chatbot</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
