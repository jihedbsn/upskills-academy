@extends('layouts.app')

@push('styles')
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('title', 'Éditeur de Certificat - ' . $certif->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Zone de prévisualisation du certificat -->
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Éditeur de Certificat - Cliquez sur l'image pour positionner les champs
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Champs positionnés sur le certificat -->
                    <div id="name_studentDiv" style="position: absolute; top: 0px; left: 0px; display: none;">
                        Nom de l'Étudiant
                    </div>
                    <div id="dateDiv" style="position: absolute; top: 0px; left: 0px; display: none;">
                        Date
                    </div>
                    <div id="serial_numberDiv" style="position: absolute; top: 0px; left: 0px; display: none;">
                        Numéro de Série
                    </div>
                    <div id="qr_codeDiv" style="position: absolute; top: 0px; left: 0px; display: none;">
                        <img src="{{ asset('assets/admin/img/qr.jpg') }}" alt="QR Code">
                    </div>
                    
                    <!-- Zone de clic sur l'image -->
                    <div id="certificate-div" onclick="drawTextToCertificate(event)" onmousemove="getPosition(event)" style="border: 1px solid #c4c4c4; position: relative;">
                        @if($certif->image_url)
                            <img src="{{ asset($certif->image_url) }}" id="image-img" width="800px" alt="Certificat">
                        @else
                            <div class="text-center p-5">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune image de certificat trouvée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Panneau de contrôles -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Contrôles</h6>
                </div>
                <div class="card-body">
                    <!-- Élément actif -->
                    <div class="form-group mb-3">
                        <label>Élément Actif:</label>
                        <input type="text" class="form-control form-control-sm" name="activeElement" id="activeElement" value="name_student" readonly>
                    </div>

                    <!-- Position de la souris -->
                    <div class="form-group mb-3">
                        <label>Position de la Souris:</label>
                        <div id="position" class="small text-muted">X: 0, Y: 0</div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="form-group mb-3">
                        <button type="button" class="btn btn-success btn-sm w-100 mb-2" onclick="saveCertificate()">
                            <i class="fas fa-save me-1"></i>Sauvegarder
                        </button>
                        <button type="button" class="btn btn-warning btn-sm w-100" onclick="resetCertificate()">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                    </div>

                    <!-- Orientation -->
                    <div class="form-group mb-3">
                        <label>Orientation:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orientation" value="vertical" checked>
                            <label class="form-check-label">Vertical</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orientation" value="horizontal">
                            <label class="form-check-label">Horizontal</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panneaux accordéon pour chaque champ -->
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Configuration des Champs</h6>
                </div>
                <div class="card-body p-0">
                    <div class="accordion" id="accordionFields">
                        <!-- Nom de l'Étudiant -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" onclick="activeElementChange('name_student')">
                                    <i class="fas fa-user me-2"></i>Nom de l'Étudiant
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#accordionFields">
                                <div class="accordion-body">
                                    <div class="form-group mb-2">
                                        <label class="small">Position X et Y:</label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="name_studentX" name="name_studentX" onchange="drawTextTFromInput()">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="name_studentY" name="name_studentY" onchange="drawTextTFromInput()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Largeur:</label>
                                        <input type="number" class="form-control form-control-sm" id="name_studentWidth" name="name_studentWidth" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Hauteur:</label>
                                        <input type="number" class="form-control form-control-sm" id="name_studentHeight" name="name_studentHeight" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Texte:</label>
                                        <input type="text" class="form-control form-control-sm" id="name_student" name="name_student" value="Nom de l'Étudiant" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Taille de police:</label>
                                        <input type="number" class="form-control form-control-sm" id="name_studentFont" name="name_studentFont" value="16" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Couleur:</label>
                                        <input type="color" class="form-control form-control-sm" id="name_studentColor" name="name_studentColor" value="#000000" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="name_studentChecked" name="name_studentChecked" checked onchange="drawTextTFromInput()">
                                        <label class="form-check-label small">Afficher sur le certificat</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" onclick="activeElementChange('date')">
                                    <i class="fas fa-calendar me-2"></i>Date
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#accordionFields">
                                <div class="accordion-body">
                                    <div class="form-group mb-2">
                                        <label class="small">Position X et Y:</label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="dateX" name="dateX" onchange="drawTextTFromInput()">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="dateY" name="dateY" onchange="drawTextTFromInput()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Largeur:</label>
                                        <input type="number" class="form-control form-control-sm" id="dateWidth" name="dateWidth" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Hauteur:</label>
                                        <input type="number" class="form-control form-control-sm" id="dateHeight" name="dateHeight" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Date:</label>
                                        <input type="date" class="form-control form-control-sm" id="date" name="date" value="{{ date('Y-m-d') }}" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Taille de police:</label>
                                        <input type="number" class="form-control form-control-sm" id="dateFont" name="dateFont" value="14" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Couleur:</label>
                                        <input type="color" class="form-control form-control-sm" id="dateColor" name="dateColor" value="#000000" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dateChecked" name="dateChecked" checked onchange="drawTextTFromInput()">
                                        <label class="form-check-label small">Afficher sur le certificat</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Numéro de Série -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" onclick="activeElementChange('serial_number')">
                                    <i class="fas fa-hashtag me-2"></i>Numéro de Série
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#accordionFields">
                                <div class="accordion-body">
                                    <div class="form-group mb-2">
                                        <label class="small">Position X et Y:</label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="serial_numberX" name="serial_numberX" onchange="drawTextTFromInput()">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="serial_numberY" name="serial_numberY" onchange="drawTextTFromInput()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Largeur:</label>
                                        <input type="number" class="form-control form-control-sm" id="serial_numberWidth" name="serial_numberWidth" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Hauteur:</label>
                                        <input type="number" class="form-control form-control-sm" id="serial_numberHeight" name="serial_numberHeight" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Numéro:</label>
                                        <input type="text" class="form-control form-control-sm" id="serial_number" name="serial_number" value="123456789" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Taille de police:</label>
                                        <input type="number" class="form-control form-control-sm" id="serial_numberFont" name="serial_numberFont" value="12" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Couleur:</label>
                                        <input type="color" class="form-control form-control-sm" id="serial_numberColor" name="serial_numberColor" value="#000000" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="serial_numberChecked" name="serial_numberChecked" checked onchange="drawTextTFromInput()">
                                        <label class="form-check-label small">Afficher sur le certificat</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" onclick="activeElementChange('qr_code')">
                                    <i class="fas fa-qrcode me-2"></i>QR Code
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#accordionFields">
                                <div class="accordion-body">
                                    <div class="form-group mb-2">
                                        <label class="small">Position X et Y:</label>
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="qr_codeX" name="qr_codeX" onchange="drawTextTFromInput()">
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control form-control-sm" id="qr_codeY" name="qr_codeY" onchange="drawTextTFromInput()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Largeur:</label>
                                        <input type="number" class="form-control form-control-sm" id="qr_codeWidth" name="qr_codeWidth" value="100" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">Hauteur:</label>
                                        <input type="number" class="form-control form-control-sm" id="qr_codeHeight" name="qr_codeHeight" value="100" onchange="drawTextTFromInput()">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small">QR Code:</label>
                                        <input type="text" class="form-control form-control-sm" value="QR Code" readonly>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="qr_codeChecked" name="qr_codeChecked" checked onchange="drawTextTFromInput()">
                                        <label class="form-check-label small">Afficher sur le certificat</label>
                                    </div>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Variables globales
let certificateData = {};

// Fonction pour obtenir la position exacte d'un élément
function getHelperPosition(el) {
    var xPosition = 0;
    var yPosition = 0;

    while (el) {
        if (el.tagName == "BODY") {
            var xScrollPos = el.scrollLeft || document.documentElement.scrollLeft;
            var yScrollPos = el.scrollTop || document.documentElement.scrollTop;

            xPosition += (el.offsetLeft - xScrollPos + el.clientLeft);
            yPosition += (el.offsetTop - yScrollPos + el.clientTop);
        } else {
            xPosition += (el.offsetLeft - el.scrollLeft + el.clientLeft);
            yPosition += (el.offsetTop - el.scrollTop + el.clientTop);
        }

        el = el.offsetParent;
    }
    return {
        x: xPosition,
        y: yPosition
    };
}

// Changer l'élément actif
function activeElementChange(activeElement) {
    document.getElementById('activeElement').value = activeElement;
    console.log('Élément actif changé:', activeElement);
}

// Obtenir la position de la souris
function getPosition(event) {
    var image_position = getHelperPosition(document.querySelector("#image-img"));
    var image_x = image_position.x + 14;
    var image_y = image_position.y + 14;

    var x = event.clientX + document.body.scrollLeft - image_x;
    var y = event.clientY + document.body.scrollTop - image_y;
    
    var coords = "X: " + Math.round(x) + ", Y: " + Math.round(y);
    document.getElementById('position').innerHTML = coords;
}

// Dessiner le texte sur le certificat en cliquant
function drawTextToCertificate(event) {
    var image_position = getHelperPosition(document.querySelector("#image-img"));
    var image_x = image_position.x + 14;
    var image_y = image_position.y + 14;

    var activeElement = document.getElementById('activeElement').value;
    var elWidth = $('#' + activeElement + 'Div').width();
    var elHeight = $('#' + activeElement + 'Div').height();

    var x = event.clientX + document.body.scrollLeft - image_x - (elWidth / 2);
    var y = event.clientY + document.body.scrollTop - image_y - (elHeight / 2);
    
    var coords = "X: " + Math.round(x) + ", Y: " + Math.round(y);
    document.getElementById('position').innerHTML = coords;

    // Mettre à jour les champs
    document.getElementById(activeElement + "X").value = Math.round(x);
    document.getElementById(activeElement + "Y").value = Math.round(y);

    // Dessiner l'élément
    drawTextTFromInput();
}

// Dessiner le texte à partir des inputs
function drawTextTFromInput() {
    var activeElement = document.getElementById('activeElement').value;
    
    var x = parseInt(document.getElementById(activeElement + "X").value) || 0;
    var y = parseInt(document.getElementById(activeElement + "Y").value) || 0;
    var text = document.getElementById(activeElement).value || '';
    var fontSize = document.getElementById(activeElement + "Font").value || 14;
    var color = document.getElementById(activeElement + "Color").value || '#000000';
    var checked = document.getElementById(activeElement + "Checked").checked;

    // Ajuster la position pour l'affichage
    var x_draw = x + 29;
    var y_draw = y + 29;

    var div = $('#' + activeElement + 'Div');
    
    if (checked) {
        div.css({
            "padding": "5px",
            "background": "rgba(255, 255, 255, 0.8)",
            "position": "absolute",
            "color": color,
            "font-size": fontSize + "px",
            "left": x_draw,
            "top": y_draw,
            "border": "1px solid #ccc",
            "border-radius": "3px",
            "display": "block"
        });
        
        if (activeElement === 'qr_code') {
            div.html('<img src="{{ asset("assets/admin/img/qr.jpg") }}" style="width: 100%; height: 100%; object-fit: contain;">');
        } else {
            div.html(text);
        }
        
        // Mettre à jour les dimensions
        var width = div.width();
        var height = div.height();
        document.getElementById(activeElement + "Width").value = width;
        document.getElementById(activeElement + "Height").value = height;
    } else {
        div.hide();
    }
    
    // Sauvegarder les données
    saveFieldData(activeElement, x, y, text, fontSize, color, checked);
}

// Sauvegarder les données d'un champ
function saveFieldData(fieldName, x, y, text, fontSize, color, show) {
    certificateData[fieldName] = {
        x: x,
        y: y,
        text: text,
        font_size: parseInt(fontSize),
        color: color,
        show: show,
        width: parseInt(document.getElementById(fieldName + "Width").value) || 100,
        height: parseInt(document.getElementById(fieldName + "Height").value) || 30
    };
    
    console.log('Données sauvegardées pour', fieldName, ':', certificateData[fieldName]);
}

// Sauvegarder le certificat
async function saveCertificate() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch(`/certifs/{{ $certif->id }}/template-data`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                template_data: certificateData
            })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            alert('✅ Certificat sauvegardé avec succès !');
            console.log('💾 Données sauvegardées:', certificateData);
        } else {
            console.error('❌ Erreur lors de la sauvegarde:', result.message);
            alert('❌ Erreur de sauvegarde: ' + result.message);
        }
    } catch (error) {
        console.error('❌ Erreur lors de la sauvegarde:', error);
        alert('❌ Erreur de connexion lors de la sauvegarde');
    }
}

// Reset du certificat
function resetCertificate() {
    if (confirm('Êtes-vous sûr de vouloir remettre à zéro tous les champs ?')) {
        // Reset des positions
        const fields = ['name_student', 'date', 'serial_number', 'qr_code'];
        fields.forEach(field => {
            document.getElementById(field + 'X').value = '';
            document.getElementById(field + 'Y').value = '';
            document.getElementById(field + 'Width').value = '';
            document.getElementById(field + 'Height').value = '';
            $('#' + field + 'Div').hide();
        });
        
        certificateData = {};
        console.log('🔄 Certificat remis à zéro');
    }
}

// Initialisation
$(document).ready(function() {
    console.log('🚀 Éditeur de certificat initialisé');
    
    // Charger les données existantes si disponibles
    loadExistingData();
});

// Charger les données existantes
async function loadExistingData() {
    try {
        const response = await fetch(`/certifs/{{ $certif->id }}/template-data`);
        const data = await response.json();
        
        if (data.success && data.template_data) {
            certificateData = data.template_data;
            
            // Appliquer les données aux champs
            Object.keys(certificateData).forEach(fieldName => {
                const fieldData = certificateData[fieldName];
                
                if (document.getElementById(fieldName + 'X')) {
                    document.getElementById(fieldName + 'X').value = fieldData.x || 0;
                    document.getElementById(fieldName + 'Y').value = fieldData.y || 0;
                    document.getElementById(fieldName + 'Width').value = fieldData.width || 100;
                    document.getElementById(fieldName + 'Height').value = fieldData.height || 30;
                    document.getElementById(fieldName + 'Font').value = fieldData.font_size || 14;
                    document.getElementById(fieldName + 'Color').value = fieldData.color || '#000000';
                    document.getElementById(fieldName + 'Checked').checked = fieldData.show || false;
                    
                    if (fieldData.text) {
                        document.getElementById(fieldName).value = fieldData.text;
                    }
                    
                    // Dessiner le champ
                    drawTextTFromInput();
                }
            });
            
            console.log('📊 Données existantes chargées:', certificateData);
        }
    } catch (error) {
        console.error('❌ Erreur lors du chargement des données:', error);
    }
}
</script>
@endpush
