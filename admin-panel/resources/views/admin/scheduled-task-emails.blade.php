@extends('layouts.app')

@section('title', 'Gestion des Emails de Tâches Planifiées')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fa fa-envelope me-2"></i>
                        Gestion des Emails de Tâches Planifiées
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Tâches Aujourd'hui</h6>
                                            <h3 class="mb-0">{{ $stats['today_tasks'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-calendar-day fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Tâches Demain</h6>
                                            <h3 class="mb-0">{{ $stats['tomorrow_tasks'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-calendar-plus fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Étudiants Aujourd'hui</h6>
                                            <h3 class="mb-0">{{ $stats['total_students_today'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Étudiants Demain</h6>
                                            <h3 class="mb-0">{{ $stats['total_students_tomorrow'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-user-graduate fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Actions d'Envoi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button class="btn btn-primary btn-lg w-100" onclick="sendEmails('today', false)">
                                                <i class="fa fa-paper-plane me-2"></i>
                                                Envoyer Rappels Aujourd'hui
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-success btn-lg w-100" onclick="sendEmails('tomorrow', false)">
                                                <i class="fa fa-bell me-2"></i>
                                                Envoyer Préparations Demain
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-warning btn-lg w-100" onclick="sendEmails('both', false)">
                                                <i class="fa fa-envelope-open me-2"></i>
                                                Envoyer Tous
                                            </button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button class="btn btn-info btn-lg w-100" onclick="sendEmails('today', true)">
                                                <i class="fa fa-flask me-2"></i>
                                                Tester Aujourd'hui
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-info btn-lg w-100" onclick="sendEmails('tomorrow', true)">
                                                <i class="fa fa-flask me-2"></i>
                                                Tester Demain
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-info btn-lg w-100" onclick="sendEmails('both', true)">
                                                <i class="fa fa-flask me-2"></i>
                                                Tester Tous
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tâches d'Aujourd'hui -->
                    @if($todayTasks->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fa fa-calendar-day me-2"></i>
                                        Tâches d'Aujourd'hui ({{ $todayTasks->count() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Étudiant</th>
                                                    <th>Email</th>
                                                    <th>Cours</th>
                                                    <th>Tâche</th>
                                                    <th>Heure</th>
                                                    <th>Priorité</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($todayTasks as $task)
                                                <tr>
                                                    <td>{{ $task->student->first_name }} {{ $task->student->last_name }}</td>
                                                    <td>{{ $task->student->email }}</td>
                                                    <td>{{ $task->course ? $task->course->titre : 'N/A' }}</td>
                                                    <td>{{ $task->message }}</td>
                                                    <td>{{ $task->date_time->format('H:i') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="sendTestEmail({{ $task->id }}, '{{ $task->student->email }}')">
                                                            <i class="fa fa-envelope"></i> Test
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Tâches de Demain -->
                    @if($tomorrowTasks->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fa fa-calendar-plus me-2"></i>
                                        Tâches de Demain ({{ $tomorrowTasks->count() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Étudiant</th>
                                                    <th>Email</th>
                                                    <th>Cours</th>
                                                    <th>Tâche</th>
                                                    <th>Heure</th>
                                                    <th>Priorité</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tomorrowTasks as $task)
                                                <tr>
                                                    <td>{{ $task->student->first_name }} {{ $task->student->last_name }}</td>
                                                    <td>{{ $task->student->email }}</td>
                                                    <td>{{ $task->course ? $task->course->titre : 'N/A' }}</td>
                                                    <td>{{ $task->message }}</td>
                                                    <td>{{ $task->date_time->format('H:i') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="sendTestEmail({{ $task->id }}, '{{ $task->student->email }}')">
                                                            <i class="fa fa-envelope"></i> Test
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Information Scheduler -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle me-2"></i>Configuration du Scheduler</h5>
                                <p>Pour que les emails s'envoient automatiquement, ajoutez cette ligne à votre crontab :</p>
                                <code>* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                                <hr>
                                <p><strong>Alternative :</strong> Utilisez les boutons ci-dessus pour envoyer manuellement les emails.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour email de test -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Envoyer Email de Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label for="testEmail" class="form-label">Adresse Email de Test</label>
                        <input type="email" class="form-control" id="testEmail" required>
                    </div>
                    <input type="hidden" id="testTaskId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="confirmTestEmail()">Envoyer Test</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendEmails(timing, testMode) {
    const action = testMode ? 'test' : 'envoi';
    const timingLabel = timing === 'today' ? 'aujourd\'hui' : timing === 'tomorrow' ? 'demain' : 'tous';
    
    if (!testMode && !confirm(`Êtes-vous sûr de vouloir envoyer les emails pour ${timingLabel} ?`)) {
        return;
    }

    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Envoi en cours...';
    button.disabled = true;

    fetch('{{ route("scheduled-task-emails.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            timing: timing,
            test_mode: testMode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (testMode) {
                alert(`Test effectué avec succès !\n\nTâches trouvées:\n- Aujourd'hui: ${data.data.today_tasks.length}\n- Demain: ${data.data.tomorrow_tasks.length}\n- Total emails: ${data.data.total_emails}`);
                
                // Afficher les détails du test
                console.log('Détails du test:', data.data);
            } else {
                alert('Emails envoyés avec succès !');
                location.reload();
            }
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'envoi des emails');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function sendTestEmail(taskId, studentEmail) {
    document.getElementById('testTaskId').value = taskId;
    document.getElementById('testEmail').value = studentEmail;
    new bootstrap.Modal(document.getElementById('testEmailModal')).show();
}

function confirmTestEmail() {
    const taskId = document.getElementById('testTaskId').value;
    const email = document.getElementById('testEmail').value;
    
    if (!email) {
        alert('Veuillez saisir une adresse email');
        return;
    }

    fetch('{{ route("scheduled-task-emails.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            task_id: taskId,
            email: email
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email de test envoyé avec succès à ' + email);
            bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'envoi de l\'email de test');
    });
}
</script>
@endpush
