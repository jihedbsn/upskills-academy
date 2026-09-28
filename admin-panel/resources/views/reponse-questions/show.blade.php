@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails de la Réponse</h3>
                    <div class="card-tools">
                        <a href="{{ route('reponse-questions.edit', $reponseQuestion) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('reponse-questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID :</th>
                                    <td>{{ $reponseQuestion->id }}</td>
                                </tr>
                                <tr>
                                    <th>Titre (Arabe) :</th>
                                    <td>{{ $reponseQuestion->titre_ar }}</td>
                                </tr>
                                <tr>
                                    <th>Titre (Anglais) :</th>
                                    <td>{{ $reponseQuestion->titre_en }}</td>
                                </tr>
                                <tr>
                                    <th>Réponse Correcte :</th>
                                    <td>
                                        @if($reponseQuestion->is_correcte)
                                            <span class="badge badge-success">Correcte</span>
                                        @else
                                            <span class="badge badge-secondary">Incorrecte</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de création :</th>
                                    <td>{{ $reponseQuestion->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière modification :</th>
                                    <td>{{ $reponseQuestion->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Question associée</h5>
                                </div>
                                <div class="card-body">
                                    @if($reponseQuestion->question)
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="120">Question (AR) :</th>
                                                <td>{{ $reponseQuestion->question->name_ar }}</td>
                                            </tr>
                                            <tr>
                                                <th>Question (EN) :</th>
                                                <td>{{ $reponseQuestion->question->name_en }}</td>
                                            </tr>
                                            <tr>
                                                <th>Points :</th>
                                                <td>{{ $reponseQuestion->question->point }}</td>
                                            </tr>
                                            @if($reponseQuestion->question->quiz)
                                                <tr>
                                                    <th>Quiz (AR) :</th>
                                                    <td>{{ $reponseQuestion->question->quiz->name_ar }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Quiz (EN) :</th>
                                                    <td>{{ $reponseQuestion->question->quiz->name_en }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Score du Quiz :</th>
                                                    <td>{{ $reponseQuestion->question->quiz->score }}</td>
                                                </tr>
                                                @if($reponseQuestion->question->quiz->type)
                                                    <tr>
                                                        <th>Type de Quiz :</th>
                                                        <td>{{ $reponseQuestion->question->quiz->type->titre }}</td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </table>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            La question associée a été supprimée.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Autres réponses de cette question</h5>
                                </div>
                                <div class="card-body">
                                    @if($reponseQuestion->question && $reponseQuestion->question->reponses->count() > 1)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Titre (Arabe)</th>
                                                        <th>Titre (Anglais)</th>
                                                        <th>Correcte</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reponseQuestion->question->reponses as $reponse)
                                                        <tr class="{{ $reponse->id == $reponseQuestion->id ? 'table-primary' : '' }}">
                                                            <td>{{ $reponse->titre_ar }}</td>
                                                            <td>{{ $reponse->titre_en }}</td>
                                                            <td>
                                                                @if($reponse->is_correcte)
                                                                    <span class="badge badge-success">Correcte</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Incorrecte</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Cette question n'a pas d'autres réponses.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
