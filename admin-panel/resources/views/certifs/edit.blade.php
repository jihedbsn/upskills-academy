@extends('layouts.app')

@push('styles')
<!-- Bootstrap Colorpicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ============================================= */
/* PRECISION POSITIONING TOOLS */
/* ============================================= */

/* Crosshair cursor for precise positioning */
#certificate-div {
    cursor: crosshair;
    position: relative;
    display: inline-block;
    overflow: visible;
    width: fit-content;
    height: fit-content;
}

/* Visual crosshair overlay */
.crosshair-overlay {
    position: absolute;
    pointer-events: none;
    z-index: 5000;
    display: none;
}

.crosshair-horizontal {
    position: absolute;
    height: 1px;
    background: rgba(255, 0, 0, 0.5);
    width: 100%;
    left: 0;
}

.crosshair-vertical {
    position: absolute;
    width: 1px;
    background: rgba(255, 0, 0, 0.5);
    height: 100%;
    top: 0;
}

/* Grid overlay for alignment */
.grid-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 2;
    display: none;
    background-image:
        repeating-linear-gradient(0deg, transparent, transparent 49px, rgba(0, 123, 255, 0.1) 49px, rgba(0, 123, 255, 0.1) 50px),
        repeating-linear-gradient(90deg, transparent, transparent 49px, rgba(0, 123, 255, 0.1) 49px, rgba(0, 123, 255, 0.1) 50px);
}

.grid-overlay.active {
    display: block;
}

/* Coordinate display tooltip */
.coordinate-tooltip {
    position: fixed;
    background: rgba(0, 0, 0, 0.85);
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-family: 'Courier New', monospace;
    pointer-events: none;
    z-index: 10000;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    white-space: nowrap;
}

.coordinate-tooltip.active {
    display: block;
}

/* Alignment guides */
.alignment-guide {
    position: absolute;
    background: rgba(255, 0, 255, 0.6);
    pointer-events: none;
    z-index: 4999;
    display: none;
}

.alignment-guide.horizontal {
    height: 1px;
    width: 100%;
    left: 0;
}

.alignment-guide.vertical {
    width: 1px;
    height: 100%;
    top: 0;
}

.alignment-guide.active {
    display: block;
    animation: guidePulse 0.5s ease-in-out;
}

@keyframes guidePulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

/* Real-time display alert styling */
.alert-info {
    background-color: #d1ecf1 !important;
    border-color: #bee5eb !important;
    color: #000000 !important;
}

.alert-info strong {
    color: #000000 !important;
    font-weight: 700 !important;
}

.alert-info span {
    color: #000000 !important;
}

.alert-info span#activeElementName {
    color: #000000 !important;
    font-weight: 600 !important;
}

/* Styles pour les éléments draggables */
.draggable-field {
    position: absolute;
    cursor: move;
    padding: 8px 12px;
    min-width: 120px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 9999;
    user-select: none;
    border: 2px dashed transparent;
}

.draggable-field:hover {
    z-index: 9999;
    border-color: rgba(0, 123, 255, 0.5);
    background: rgba(0, 123, 255, 0.05);
}

.draggable-field.dragging {
    z-index: 10000;
    border-color: rgba(255, 193, 7, 0.8);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.field-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-weight: bold;
    color: #007bff;
    font-size: 14px;
    pointer-events: none;
}

.field-content i {
    font-size: 16px;
    display: inline-block;
    margin-right: 8px;
    margin-bottom: 0;
}

.draggable-field.dragging .field-content {
    color: #ffc107;
}

/* Styles pour les champs de formulaire cachés */
.hidden-fields {
    display: none;
}

/* S'assurer que l'image du certificat est en arrière-plan */
#image-img {
    position: relative;
    z-index: 1;
    max-width: none !important;
    width: auto !important;
    height: auto !important;
    display: block !important;
}

/* Container du certificat */
#certificate-div {
    position: relative;
    display: inline-block;
    overflow: auto;
    width: fit-content;
    height: fit-content;
}

/* Forcer les variables au-dessus de tout */
#certificate-div .draggable-field {
    position: absolute !important;
    z-index: 99999 !important;
    pointer-events: auto !important;
    background: transparent !important;
    backdrop-filter: none !important;
}
</style>
.draggable-field {
    cursor: move !important;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    z-index: 1000;
    min-width: 120px;
    min-height: 40px;
    position: absolute;
    padding: 12px;
    will-change: transform, left, top;
    /* Position box so bottom-left corner is at coordinate point */
    transform: translateY(-100%);
}

.draggable-field::before {
    /* Visual indicator at bottom-left corner (text baseline position) */
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 8px;
    height: 8px;
    background: #ff0000;
    border-radius: 50%;
    border: 2px solid white;
    z-index: 10000;
}

.draggable-field:hover {
}

.draggable-field.dragging {
    z-index: 1001;
}

/* Mode drag ultra fluide: réduire les effets coûteux */
.draggable-field.dragging-fast {
    transition: none !important;
}

/* Styles pour le mode sans limite */
.draggable-field.dragging {
    z-index: 1001;
}

/* Indicateur de position amélioré */
.position-indicator {
    position: absolute;
    top: -25px;
    left: 0;
    color: white;
    padding: 3px 8px;
    font-size: 11px;
    font-family: monospace;
    font-weight: bold;
    pointer-events: none;
    z-index: 1002;
    white-space: nowrap;
}

/* Panneau de contrôle pour le mode sans limite */
.unlimited-controls {
}

.unlimited-controls label {
}

.unlimited-controls label:hover {
}

/* Animation pour les éléments en mode sans limite */
.draggable-field.unlimited-mode {
}

@keyframes pulse-unlimited {
    0% { }
    70% { }
    100% { }
}

.draggable-field.selected {
}

#certificate-div {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

#certificate-div.drag-over {
}

.field-label {
    font-size: 10px;
    color: #666;
    margin-bottom: 2px;
    font-weight: bold;
}

/* Amélioration du contenu des champs */
.draggable-field div {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 100%;
    text-align: left;
    font-weight: 500;
    line-height: 1.4;
    word-wrap: break-word;
    overflow: hidden;
    padding: 4px;
}

/* Amélioration des champs spécifiques avec bordures de trace épaisses */
#name_studentDiv {
    padding: 0px !important;
    min-width: auto !important;
    min-height: auto !important;
}

#name_studentDiv:hover {
}

#dateDiv {
    padding: 0px !important;
    min-width: auto !important;
    min-height: auto !important;
}

#dateDiv:hover {
}

#serial_numberDiv {
    padding: 0px !important;
    min-width: auto !important;
    min-height: auto !important;
}

#serial_numberDiv:hover {
}

#qr_codeDiv {
    padding: 0px !important;
    min-width: auto !important;
    min-height: auto !important;
}

#qr_codeDiv:hover {
}

/* Styles spécifiques pour name_student - pas de retour à la ligne et taille fixe */
#name_studentDiv {
    white-space: nowrap !important;
    overflow: visible !important;
    text-overflow: clip !important;
    max-width: none !important;
    /* Taille auto pour s'adapter au contenu */
    width: auto !important;
    height: auto !important;
    min-width: auto !important;
    min-height: auto !important;
    max-width: none !important;
    max-height: none !important;
    /* Empêcher le redimensionnement automatique */
    box-sizing: border-box !important;
    resize: none !important;
}

#name_studentDiv .field-content {
    white-space: nowrap !important;
    overflow: visible !important;
    text-overflow: clip !important;
    /* Taille fixe pour le contenu */
    width: auto !important;
    height: auto !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
}

#name_studentDiv span {
    white-space: nowrap !important;
    overflow: visible !important;
    text-overflow: clip !important;
    display: inline-block !important;
    /* Le texte peut dépasser mais la div reste fixe */
    max-width: none !important;
}

/* Animation de pulsation pour les champs actifs */
.draggable-field.active {
}

@keyframes pulse {
    0% { }
    70% { }
    100% { }
}

/* Amélioration des boutons */
.btn {
}

.btn:hover {
}

/* Indicateur de position */
.position-indicator {
    position: absolute;
    width: 4px;
    height: 4px;
    z-index: 1002;
    pointer-events: none;
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}
</style>
@endpush

@section('title', 'Éditeur de Certificat - ' . $certif->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10">
            <!-- Precision Positioning Toolbar -->
            <div class="card mb-3 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-2">
                            <h6 class="mb-2"><i class="fas fa-crosshairs"></i> Precision Tools</h6>
                            <div class="btn-group btn-group-sm d-flex" role="group">
                                <button type="button" class="btn btn-outline-primary" id="gridBtn" onclick="toggleGrid()" title="Toggle Grid">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-info" id="snapBtn" onclick="toggleSnapToGrid()" title="Snap to Grid">
                                    <i class="fas fa-magnet"></i> Snap
                                </button>
                                <button type="button" class="btn btn-outline-success" id="coordsBtn" onclick="toggleCoordinateDisplay()" title="Show Coordinates">
                                    <i class="fas fa-map-marker-alt"></i> Coords
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <h6 class="mb-2"><i class="fas fa-search"></i> Zoom</h6>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="zoomOut()">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control text-center" id="zoomLevel" value="100%" readonly style="max-width: 70px;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="zoomIn()">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetZoom()">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <h6 class="mb-2"><i class="fas fa-arrows-alt"></i> Fine Adjust</h6>
                            <div class="btn-group btn-group-sm d-flex" role="group">
                                <button type="button" class="btn btn-outline-dark" onclick="nudgeField('up', 1)" title="Move Up 1px">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" onclick="nudgeField('down', 1)" title="Move Down 1px">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" onclick="nudgeField('left', 1)" title="Move Left 1px">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button type="button" class="btn btn-outline-dark" onclick="nudgeField('right', 1)" title="Move Right 1px">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <h6 class="mb-2"><i class="fas fa-align-center"></i> Alignment</h6>
                            <div class="btn-group btn-group-sm d-flex" role="group">
                                <button type="button" class="btn btn-outline-info" onclick="alignFieldToCenter('horizontal')" title="Center Horizontally">
                                    <i class="fas fa-align-justify"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="alignFieldToCenter('vertical')" title="Center Vertically">
                                    <i class="fas fa-align-center"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="showAllGuides()" title="Show Guides">
                                    <i class="fas fa-ruler-combined"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="showPrecisionHelp()" title="Help">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time position display -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0 py-2 small" role="alert" style="background-color: #d1ecf1 !important; color: #000000 !important; border-color: #bee5eb !important;">
                                <strong style="color: #000000 !important;">Active:</strong> <span id="activeElementName" style="color: #000000 !important; font-weight: 600;">name_student</span> |
                                <strong style="color: #000000 !important;">Position:</strong> X: <span id="realTimeX" style="color: #000000 !important; background: none !important; padding: 0 4px; font-weight: 600;">0</span>px, Y: <span id="realTimeY" style="color: #000000 !important; background: none !important; padding: 0 4px; font-weight: 600;">0</span>px |
                                <strong style="color: #000000 !important;">Size:</strong> <span id="realTimeWidth" style="color: #000000 !important; background: none !important; padding: 0 4px; font-weight: 600;">0</span> × <span id="realTimeHeight" style="color: #000000 !important; background: none !important; padding: 0 4px; font-weight: 600;">0</span>px
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="hidden" name="image" id="image" value="{{$certif->file_url}}"/>
                </div>
            <div id="qrcodeDiv" style="position: absolute; top: 0px; left: 0px;">
                @if(!is_null($certif->qrcodex) && !is_null($certif->qrcodey))
                    <img src='{{asset('assets/admin/img/qr.jpg')}}'>
                    <script>
                        <?php
                            $x = $certif->qrcodex;
                            $y = $certif->qrcodey;
                             $x_draw=$x+29;
                             $y_draw=$y+29
                        ?>
                    $(document).ready(function(){
                            $('#qrcodeDiv').css({
                                "padding": "0px",
                                "position": "absolute",
                                "left": {{ $x_draw }},
                                "top": {{ $y_draw }}
                            });
                            $("#qrcodeX").val("{{ $x }}");
                            $("#qrcodeY").val("{{ $y }}");
                        });
                    </script>
                @endif
                    </div>

            <div id="certificate-div" style="top: 0px; left: 0px; position: relative; transform-origin: top left;">
                <!-- Grid Overlay -->
                <div class="grid-overlay" id="gridOverlay"></div>

                <!-- Alignment Guides -->
                <div class="alignment-guide horizontal" id="alignGuideH"></div>
                <div class="alignment-guide vertical" id="alignGuideV"></div>

                <img src="{{$certif->file_url}}" id="image-img" style="max-width: none; width: auto; height: auto; display: block; position: relative; z-index: 1;">

                <!-- Éléments draggables pour les variables - Après l'image -->
                <div id="name_studentDiv" class="draggable-field" style="left: {{ $certif->template_data['name_student']['x'] ?? 100 }}px; top: {{ $certif->template_data['name_student']['y'] ?? 100 }}px; position: absolute; z-index: 99999; width: {{ $certif->template_data['name_student']['width'] ?? 200 }}px;">
                    <div class="field-content" style="justify-content: center !important; flex-direction: row !important; text-align: center;">
                        <i class="fas fa-user" style="margin-right: 8px; font-size: 20px;"></i>
                        <span id="name_studentSpan" style="text-align: center; font-weight: 500; line-height: 1.4; word-wrap: break-word; font-size: {{ $certif->template_data['name_student']['font_size'] ?? 50 }}px; color: {{ $certif->template_data['name_student']['color'] ?? '#000000' }};">{{ $certif->template_data['name_student']['text'] ?? 'Nom de l\'Étudiant' }}</span>
                    </div>
                </div>

                <div id="dateDiv" class="draggable-field" style="left: {{ $certif->template_data['date']['x'] ?? 100 }}px; top: {{ $certif->template_data['date']['y'] ?? 200 }}px; position: absolute; z-index: 99999; display: block; width: {{ $certif->template_data['date']['width'] ?? 150 }}px;">
                    <div class="field-content" style="justify-content: flex-start !important; flex-direction: row !important;">
                        <i class="fas fa-calendar" style="margin-right: 8px;"></i>
                        <span id="dateSpan" style="text-align: left; font-weight: 500; line-height: 1.4; word-wrap: break-word; font-size: {{ $certif->template_data['date']['font_size'] ?? 24 }}px; color: {{ $certif->template_data['date']['color'] ?? '#000000' }};">{{ $certif->template_data['date']['text'] ?? 'Date: 2024-11-29' }}</span>
                    </div>
                </div>

                <div id="serial_numberDiv" class="draggable-field" style="left: {{ $certif->template_data['serial_number']['x'] ?? 100 }}px; top: {{ $certif->template_data['serial_number']['y'] ?? 300 }}px; position: absolute; z-index: 99999; display: block; width: {{ $certif->template_data['serial_number']['width'] ?? 150 }}px;">
                    <div class="field-content" style="justify-content: flex-start !important; flex-direction: row !important;">
                        <i class="fas fa-hashtag" style="margin-right: 8px;"></i>
                        <span id="serial_numberSpan" style="text-align: left; font-weight: 500; line-height: 1.4; word-wrap: break-word; font-size: {{ $certif->template_data['serial_number']['font_size'] ?? 20 }}px; color: {{ $certif->template_data['serial_number']['color'] ?? '#000000' }};">{{ $certif->template_data['serial_number']['text'] ?? 'TEST-1764416379532' }}</span>
                    </div>
                </div>

                <div id="qr_codeDiv" class="draggable-field" style="left: {{ $certif->template_data['qr_code']['x'] ?? 100 }}px; top: {{ $certif->template_data['qr_code']['y'] ?? 400 }}px; position: absolute; z-index: 99999;">
                    <div class="field-content">
                        <i class="fas fa-qrcode"></i>
                        <span>{{ $certif->template_data['qr_code']['text'] ?? 'QR Code' }}</span>
                    </div>
                </div>
            </div>
                                    </div>
        <div class="col-md-2">

            <div class="form-group">
                <label>Active Element:</label>
                <input type="text" class="form-control" name="activeElement" id="activeElement" value="name_student" disabled >
            </div>
            <div class="form-group">
                <label>Mouse Position:</label>
                <div id="position"></div>
            </div>

            <!-- Gestion de la taille de police pour name_student -->
            <div class="form-group" id="fontSizeControl" style="display: none;">
                <label>Font Size Control:</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="decreaseActiveElementFontSize()" title="Diminuer la taille">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <input type="number" class="form-control text-center font-weight-bold" id="activeElementFontSize" value="70" onchange="updateActiveElementFontSize()" oninput="updateActiveElementFontSize()" min="8" max="200" style="font-size: 12px;">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="increaseActiveElementFontSize()" title="Augmenter la taille">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">Taille: <span id="activeElementFontDisplay">70</span>px</small>
            </div>

            <div class="form-group">
                <div class="col-md-6">
                    <a class="btn btn-file" onclick="openKCFinder($('#image-img'),$('#image'))">Select Certificate</a>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-success btn-sm w-100 mb-1" onclick="saveCertificateData()">
                    <i class="fas fa-save"></i> Sauvegarder
                </button>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary btn-sm w-100 mb-1" onclick="generateCertificateWithFakeData()">
                    <i class="fas fa-certificate"></i> Générer avec Données Factices
                </button>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-info btn-sm w-100 mb-1" onclick="debugImageDimensions()">
                    <i class="fas fa-ruler"></i> Debug Dimensions
                </button>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-warning btn-sm w-100 mb-1" onclick="testCurrentPositions()">
                    <i class="fas fa-bug"></i> Test Positions Actuelles
                </button>
            </div>

            <!-- Champs cachés pour stocker les positions -->
            <div class="hidden-fields">
                <input type="hidden" id="name_studentX" name="template_data[name_student][x]" value="{{ $certif->template_data['name_student']['x'] ?? 100 }}">
                <input type="hidden" id="name_studentY" name="template_data[name_student][y]" value="{{ $certif->template_data['name_student']['y'] ?? 100 }}">
                <input type="hidden" id="name_studentWidth" name="template_data[name_student][width]" value="{{ $certif->template_data['name_student']['width'] ?? 200 }}">
                <input type="hidden" id="name_studentHeight" name="template_data[name_student][height]" value="{{ $certif->template_data['name_student']['height'] ?? 30 }}">
                <input type="hidden" id="name_student" name="template_data[name_student][text]" value="{{ $certif->template_data['name_student']['text'] ?? 'Nom Étudiant' }}">
                <input type="hidden" id="name_studentFont" name="template_data[name_student][font_size]" value="{{ $certif->template_data['name_student']['font_size'] ?? 50 }}">
                <input type="hidden" id="name_studentColor" name="template_data[name_student][color]" value="{{ $certif->template_data['name_student']['color'] ?? '#000000' }}">
                <input type="hidden" id="name_studentUppercase" name="template_data[name_student][uppercase]" value="{{ $certif->template_data['name_student']['uppercase'] ?? 1 }}">
                <input type="hidden" id="name_studentBold" name="template_data[name_student][bold]" value="{{ $certif->template_data['name_student']['bold'] ?? 1 }}">
                <input type="hidden" id="name_studentChecked" name="template_data[name_student][show]" value="1" checked>

                <input type="hidden" id="dateX" name="template_data[date][x]" value="{{ $certif->template_data['date']['x'] ?? 100 }}">
                <input type="hidden" id="dateY" name="template_data[date][y]" value="{{ $certif->template_data['date']['y'] ?? 200 }}">
                <input type="hidden" id="dateWidth" name="template_data[date][width]" value="{{ $certif->template_data['date']['width'] ?? 150 }}">
                <input type="hidden" id="dateHeight" name="template_data[date][height]" value="{{ $certif->template_data['date']['height'] ?? 30 }}">
                <input type="hidden" id="date" name="template_data[date][text]" value="{{ $certif->template_data['date']['text'] ?? 'Date' }}">
                <input type="hidden" id="dateFont" name="template_data[date][font_size]" value="{{ $certif->template_data['date']['font_size'] ?? 14 }}">
                <input type="hidden" id="dateColor" name="template_data[date][color]" value="{{ $certif->template_data['date']['color'] ?? '#000000' }}">
                <input type="hidden" id="dateUppercase" name="template_data[date][uppercase]" value="{{ $certif->template_data['date']['uppercase'] ?? 0 }}">
                <input type="hidden" id="dateBold" name="template_data[date][bold]" value="{{ $certif->template_data['date']['bold'] ?? 0 }}">
                <input type="hidden" id="dateChecked" name="template_data[date][show]" value="1" checked>

                <input type="hidden" id="serial_numberX" name="template_data[serial_number][x]" value="{{ $certif->template_data['serial_number']['x'] ?? 100 }}">
                <input type="hidden" id="serial_numberY" name="template_data[serial_number][y]" value="{{ $certif->template_data['serial_number']['y'] ?? 300 }}">
                <input type="hidden" id="serial_numberWidth" name="template_data[serial_number][width]" value="{{ $certif->template_data['serial_number']['width'] ?? 150 }}">
                <input type="hidden" id="serial_numberHeight" name="template_data[serial_number][height]" value="{{ $certif->template_data['serial_number']['height'] ?? 30 }}">
                <input type="hidden" id="serial_number" name="template_data[serial_number][text]" value="{{ $certif->template_data['serial_number']['text'] ?? 'Numéro Série' }}">
                <input type="hidden" id="serial_numberFont" name="template_data[serial_number][font_size]" value="{{ $certif->template_data['serial_number']['font_size'] ?? 30 }}">
                <input type="hidden" id="serial_numberColor" name="template_data[serial_number][color]" value="{{ $certif->template_data['serial_number']['color'] ?? '#000000' }}">
                <input type="hidden" id="serial_numberUppercase" name="template_data[serial_number][uppercase]" value="{{ $certif->template_data['serial_number']['uppercase'] ?? 0 }}">
                <input type="hidden" id="serial_numberBold" name="template_data[serial_number][bold]" value="{{ $certif->template_data['serial_number']['bold'] ?? 0 }}">
                <input type="hidden" id="serial_numberChecked" name="template_data[serial_number][show]" value="1" checked>

                <input type="hidden" id="qr_codeX" name="template_data[qr_code][x]" value="{{ $certif->template_data['qr_code']['x'] ?? 100 }}">
                <input type="hidden" id="qr_codeY" name="template_data[qr_code][y]" value="{{ $certif->template_data['qr_code']['y'] ?? 400 }}">
                <input type="hidden" id="qr_codeWidth" name="template_data[qr_code][width]" value="{{ $certif->template_data['qr_code']['width'] ?? 100 }}">
                <input type="hidden" id="qr_codeHeight" name="template_data[qr_code][height]" value="{{ $certif->template_data['qr_code']['height'] ?? 100 }}">
                <input type="hidden" id="qr_code" name="template_data[qr_code][text]" value="{{ $certif->template_data['qr_code']['text'] ?? 'QR Code' }}">
                <input type="hidden" id="qr_codeFont" name="template_data[qr_code][font_size]" value="{{ $certif->template_data['qr_code']['font_size'] ?? 12 }}">
                <input type="hidden" id="qr_codeColor" name="template_data[qr_code][color]" value="{{ $certif->template_data['qr_code']['color'] ?? '#000000' }}">
                <input type="hidden" id="qr_codeUppercase" name="template_data[qr_code][uppercase]" value="{{ $certif->template_data['qr_code']['uppercase'] ?? 0 }}">
                <input type="hidden" id="qr_codeBold" name="template_data[qr_code][bold]" value="{{ $certif->template_data['qr_code']['bold'] ?? 0 }}">
                <input type="hidden" id="qr_codeType" name="template_data[qr_code][type]" value="qr">
                <input type="hidden" id="qr_codeChecked" name="template_data[qr_code][show]" value="1" checked>
            </div>
            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="image_width" value="{{ $certif->image_width ?? 1000 }}" checked="checked">Vertical
                </label>
                <label class="radio-inline">
                    <input type="radio" name="image_width" value="{{ $certif->image_width ?? 1415 }}">Horizontal
                </label>
                                        </div>

            <div class="panel-group form-group" id="accordion">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" onmousedown="activeElementChange('name_student')">
                                Nom de l'Étudiant
                            </a>
                        </h4>
                                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="name_studentX" name="name_studentX" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="name_studentY" name="name_studentY" onfocusout="drawTextTFromInput()">
                                        </div>

                            <div class="form-group col-sm-12">
                                <label>Width:</label>
                                <input type="text" class="form-control" id="name_studentWidth" name="name_studentWidth" onfocusout="drawTextTFromInput()">
                                        </div>
                            <div class="form-group col-sm-12">
                                <label>Height:</label>
                                <input type="text" class="form-control" id="name_studentHeight" name="name_studentHeight" onfocusout="drawTextTFromInput()">
                                        </div>

                            <div class="checkbox1">
                                <label>
                                    <input type="checkbox" id="name_studentChecked" name="name_studentChecked"  checked="checked">
                                    Show on Certificate
                                </label>
                                    </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <input type="text" class="form-control" id="name_student" name="name_student" value="Nom de l'Étudiant" onfocusout="drawTextTFromInput()">
                                </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-outline-danger" onclick="decreaseFontSize('name_student')" title="Diminuer la taille de police">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="number" class="form-control text-center font-weight-bold" id="name_studentFont" name="name_studentFont" value="{{ $certif->template_data['name_student']['font_size'] ?? 50 }}" onchange="updateFieldStyle('name_student'); updateFontDisplay('name_student')" oninput="updateFontDisplay('name_student')" min="8" max="200" style="font-size: 16px;">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" onclick="increaseFontSize('name_student')" title="Augmenter la taille de police">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Taille actuelle: <span id="name_studentFontDisplay">{{ $certif->template_data['name_student']['font_size'] ?? 50 }}</span>px</small>
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="name_studentColor" name="name_studentColor" value="{{ $certif->template_data['name_student']['color'] ?? '#000000' }}" onchange="updateFieldStyle('name_student')">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Uppercase:</label>
                            <input type="checkbox" class="form-check-input" id="name_studentUppercase" name="name_studentUppercase" {{ ($certif->template_data['name_student']['uppercase'] ?? 0) == 1 ? 'checked' : '' }} onchange="updateFieldStyle('name_student')">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Bold:</label>
                            <input type="checkbox" class="form-check-input" id="name_studentBold" name="name_studentBold" {{ ($certif->template_data['name_student']['bold'] ?? 0) == 1 ? 'checked' : '' }} onchange="updateFieldStyle('name_student')">
                        </div>

                        <div class="form-group col-sm-12">
                            <label>Center inside box:</label>
                            <input type="checkbox" class="form-check-input" id="name_studentCenter" name="name_studentCenter" checked onchange="updateFieldStyle('name_student')">
                            <small class="form-text text-muted">Centers the name within its configured width.</small>
                        </div>

                    </div>
                </div>
            </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" onmousedown="activeElementChange('date')" >
                                Date
                            </a>
                        </h4>
        </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="dateX" name="dateX" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="dateY" name="dateY" onfocusout="drawTextTFromInput()">
                </div>

                            <div class="form-group col-sm-12">
                                <label>Width:</label>
                                <input type="text" class="form-control" id="dateWidth" name="dateWidth" onfocusout="drawTextTFromInput()">
                        </div>
                            <div class="form-group col-sm-12">
                                <label>Height:</label>
                                <input type="text" class="form-control" id="dateHeight" name="dateHeight" onfocusout="drawTextTFromInput()">
                                </div>

                            <div class="checkbox1">
                                <label>
                                    <input type="checkbox" id="dateChecked" name="dateChecked" checked="checked">
                                    Show on Certificate
                                            </label>
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <?php $date = (empty($certif->date)) ? date("Y-m-d") : $certif->date;?>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>" onfocusout="drawTextTFromInput()">
                        </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <input type="number" class="form-control text-center" id="dateFont" name="dateFont" value="{{ $certif->template_data['date']['font_size'] ?? 14 }}" onchange="updateFieldStyle('date')">
                                </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="dateColor" name="dateColor" value="{{ $certif->template_data['date']['color'] ?? '#000000' }}" onchange="updateFieldStyle('date')">
                                </div>

                        <div class="form-group col-sm-6">
                            <label>Uppercase:</label>
                            <input type="checkbox" class="form-check-input" id="dateUppercase" name="dateUppercase" {{ ($certif->template_data['date']['uppercase'] ?? 0) == 1 ? 'checked' : '' }} onchange="updateFieldStyle('date')">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Bold:</label>
                            <input type="checkbox" class="form-check-input" id="dateBold" name="dateBold" {{ ($certif->template_data['date']['bold'] ?? 0) == 1 ? 'checked' : '' }} onchange="updateFieldStyle('date')">
                        </div>

                            </div>
            <div class="panel-group form-group" id="accordion">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" onmousedown="activeElementChange('name_student')">
                                Nom de l'Étudiant
                            </a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="name_studentX" name="name_studentX" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="name_studentY" name="name_studentY" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <input type="text" class="form-control" id="name_student" name="name_student" value="Nom de l'Étudiant" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <input type="number" class="form-control text-center" id="name_studentFont" name="name_studentFont" value="32" onchange="updateFieldStyle('name_student')">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="name_studentColor" name="name_studentColor" value="#000000" onchange="updateFieldStyle('name_student')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Uppercase:</label>
                                <input type="checkbox" class="form-check-input" id="name_studentUppercase" name="name_studentUppercase" checked onchange="updateFieldStyle('name_student')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Bold:</label>
                                <input type="checkbox" class="form-check-input" id="name_studentBold" name="name_studentBold" checked onchange="updateFieldStyle('name_student')">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" onmousedown="activeElementChange('date')">
                                Date
                            </a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="dateX" name="dateX" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="dateY" name="dateY" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <?php $date = (empty($certif->date)) ? date("Y-m-d") : $certif->date;?>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <input type="number" class="form-control text-center" id="dateFont" name="dateFont" value="14" onchange="updateFieldStyle('date')">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="dateColor" name="dateColor" value="#000000" onchange="updateFieldStyle('date')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Uppercase:</label>
                                <input type="checkbox" class="form-check-input" id="dateUppercase" name="dateUppercase" onchange="updateFieldStyle('date')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Bold:</label>
                                <input type="checkbox" class="form-check-input" id="dateBold" name="dateBold" onchange="updateFieldStyle('date')">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" onmousedown="activeElementChange('serial_number')">
                                Serial Number
                            </a>
                        </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="serial_numberX" name="serial_numberX" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="serial_numberY" name="serial_numberY" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" value="54654545465465" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <input type="number" class="form-control text-center" id="serial_numberFont" name="serial_numberFont" value="30" onchange="updateFieldStyle('serial_number')">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="serial_numberColor" name="serial_numberColor" value="#000000" onchange="updateFieldStyle('serial_number')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Uppercase:</label>
                                <input type="checkbox" class="form-check-input" id="serial_numberUppercase" name="serial_numberUppercase" onchange="updateFieldStyle('serial_number')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Bold:</label>
                                <input type="checkbox" class="form-check-input" id="serial_numberBold" name="serial_numberBold" onchange="updateFieldStyle('serial_number')">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse4" onmousedown="activeElementChange('qr_code')">
                                Qr
                            </a>
                        </h4>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse">
                        <div class="panel-body" style="padding: 10px;">

                            <div class="form-group col-sm-12">
                                <label style="width: 100%">Position X and Y :</label>
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="qr_codeX" name="qrcodex" onfocusout="drawTextTFromInput()">
                                <input type="text" class="form-control col-sm-12" style="width: 45%; " id="qr_codeY" name="qrcodey" onfocusout="drawTextTFromInput()">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Text:</label>
                                <input type="text" class="form-control" id="qr_code" value="<img src='{{asset('assets/admin/img/qr.jpg')}}'>" disabled>
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Font size:</label>
                                <input type="number" class="form-control text-center" id="qr_codeFont" name="qr_codeFont" value="12" onchange="updateFieldStyle('qr_code')">
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Color:</label>
                                <input type="text" class="form-control" id="qr_codeColor" name="qr_codeColor" value="#000000" onchange="updateFieldStyle('qr_code')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Uppercase:</label>
                                <input type="checkbox" class="form-check-input" id="qr_codeUppercase" name="qr_codeUppercase" onchange="updateFieldStyle('qr_code')">
                            </div>

                            <div class="form-group col-sm-6">
                                <label>Bold:</label>
                                <input type="checkbox" class="form-check-input" id="qr_codeBold" name="qr_codeBold" onchange="updateFieldStyle('qr_code')">
                            </div>

                        </div>
                    </div>
                </div>

            </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>

<script type="text/javascript">
"use strict";

// ============================================
// CERTIFICATE EDITOR - PRECISION POSITIONING
// ============================================

// ============================================
// GLOBAL STATE VARIABLES
// ============================================
let currentZoom = 1.0;
let gridEnabled = false;
let snapToGrid = false;
let coordinateDisplayEnabled = false;
let gridSize = 50; // pixels

// ============================================
// GLOBALLY ACCESSIBLE FUNCTIONS (for onclick handlers)
// ============================================

// Save certificate data
window.saveCertificateData = async function() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Error: CSRF token not found');
            return;
        }

        const templateData = {
            name_student: collectFieldData('name_student', 'Nom de l\'Étudiant'),
            date: collectFieldData('date', 'Date'),
            serial_number: collectFieldData('serial_number', 'Numéro de Série'),
            qr_code: collectFieldData('qr_code', 'QR Code')
        };

        const response = await fetch('/certifs/{{ $certif->id }}/template-data', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ template_data: templateData })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('✅ Certificate data saved successfully!');
        } else {
            console.error('❌ Save failed:', result);
            alert('❌ Error saving: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('❌ Error:', error);
        alert('❌ Error: ' + error.message);
    }
};

// Generate certificate with fake data
window.generateCertificateWithFakeData = async function() {
    try {
        const templateData = {
            name_student: collectFieldData('name_student'),
            date: collectFieldData('date'),
            serial_number: collectFieldData('serial_number'),
            qr_code: collectFieldData('qr_code')
        };

        const testData = {
            fullname_en: 'John Doe',
            date: new Date().toISOString().split('T')[0],
            serial_number: 'TEST-' + Date.now(),
            template_data: templateData
        };

        const response = await fetch('/certifs/{{ $certif->id }}/test-generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(testData)
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Test certificate generated! Check the download.');
            if (result.download_url) {
                window.open(result.download_url, '_blank');
            }
        } else {
            console.error('❌ Generation failed:', result);
            alert('❌ Error: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('❌ Error:', error);
        alert('❌ Error: ' + error.message);
    }
};

// Debug image dimensions
window.debugImageDimensions = function() {
    const img = document.getElementById('image-img');
    const certDiv = document.getElementById('certificate-div');

    if (!img || !certDiv) {
        console.error('Elements not found');
        return;
    }

    const imgRect = img.getBoundingClientRect();
    const certRect = certDiv.getBoundingClientRect();

    const debugInfo = {
        image: {
            naturalWidth: img.naturalWidth,
            naturalHeight: img.naturalHeight,
            displayWidth: imgRect.width,
            displayHeight: imgRect.height
        },
        container: {
            width: certRect.width,
            height: certRect.height
        },
        zoom: currentZoom
    };

    alert('Debug info logged to console. Check browser console (F12).');
};

// Test current positions
window.testCurrentPositions = async function() {
    try {
        const positions = {
            name_student: getElementPosition('name_studentDiv'),
            date: getElementPosition('dateDiv'),
            serial_number: getElementPosition('serial_numberDiv'),
            qr_code: getElementPosition('qr_codeDiv')
        };

        alert('Position data logged to console. Check browser console (F12).');
    } catch (error) {
        console.error('❌ Error:', error);
        alert('❌ Error: ' + error.message);
    }
};

// Precision tool functions
window.toggleGrid = function() {
    gridEnabled = !gridEnabled;
    const gridOverlay = document.getElementById('gridOverlay');
    const btn = document.getElementById('gridBtn');

    if (gridEnabled) {
        gridOverlay?.classList.add('active');
        btn?.classList.replace('btn-outline-primary', 'btn-primary');
    } else {
        gridOverlay?.classList.remove('active');
        btn?.classList.replace('btn-primary', 'btn-outline-primary');
    }
};

window.toggleSnapToGrid = function() {
    snapToGrid = !snapToGrid;
    const btn = document.getElementById('snapBtn');

    if (snapToGrid) {
        btn?.classList.replace('btn-outline-info', 'btn-info');
        btn?.classList.add('text-white');
    } else {
        btn?.classList.replace('btn-info', 'btn-outline-info');
        btn?.classList.remove('text-white');
    }
};

window.toggleCoordinateDisplay = function() {
    coordinateDisplayEnabled = !coordinateDisplayEnabled;
    const btn = document.getElementById('coordsBtn');
    const tooltip = document.getElementById('coordinateTooltip');

    if (coordinateDisplayEnabled) {
        btn?.classList.replace('btn-outline-success', 'btn-success');
        btn?.classList.add('text-white');
        tooltip?.classList.add('active');
    } else {
        btn?.classList.replace('btn-success', 'btn-outline-success');
        btn?.classList.remove('text-white');
        tooltip?.classList.remove('active');
    }
};

window.zoomIn = function() {
    currentZoom = Math.min(currentZoom + 0.1, 3.0);
    applyZoom();
};

window.zoomOut = function() {
    currentZoom = Math.max(currentZoom - 0.1, 0.5);
    applyZoom();
};

window.resetZoom = function() {
    currentZoom = 1.0;
    applyZoom();
};

window.nudgeField = function(direction, pixels = 1) {
    const activeElement = document.getElementById('activeElement')?.value;
    if (!activeElement) return;

    const field = document.getElementById(activeElement + 'Div');
    if (!field) return;

    let left = parseInt(field.style.left) || 0;
    let top = parseInt(field.style.top) || 0;

    switch(direction) {
        case 'up': top -= pixels; break;
        case 'down': top += pixels; break;
        case 'left': left -= pixels; break;
        case 'right': left += pixels; break;
    }

    if (snapToGrid) {
        left = Math.round(left / gridSize) * gridSize;
        top = Math.round(top / gridSize) * gridSize;
    }

    updateFieldPosition(activeElement + 'Div', left, top);
    updateRealTimeDisplay();
};

window.alignFieldToCenter = function(axis) {
    const activeElement = document.getElementById('activeElement')?.value;
    if (!activeElement) return;

    const field = document.getElementById(activeElement + 'Div');
    const img = document.getElementById('image-img');
    if (!field || !img) return;

    const imgRect = img.getBoundingClientRect();
    const fieldRect = field.getBoundingClientRect();

    let left = parseInt(field.style.left) || 0;
    let top = parseInt(field.style.top) || 0;

    if (axis === 'horizontal') {
        left = (imgRect.width - fieldRect.width) / 2;
    } else if (axis === 'vertical') {
        top = (imgRect.height - fieldRect.height) / 2;
    }

    updateFieldPosition(activeElement + 'Div', Math.round(left), Math.round(top));
    updateRealTimeDisplay();
};

window.showAllGuides = function() {
    const img = document.getElementById('image-img');
    if (!img) return;

    const imgRect = img.getBoundingClientRect();
    showAlignmentGuide('vertical', imgRect.width / 2);
    showAlignmentGuide('horizontal', imgRect.height / 2);
};

window.showPrecisionHelp = function() {
    const helpHTML = `
        <div class="modal fade" id="helpModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-question-circle"></i> Precision Positioning Guide</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h6><i class="fas fa-keyboard"></i> Keyboard Shortcuts</h6>
                        <ul>
                            <li><kbd>Arrow Keys</kbd> - Move 1px</li>
                            <li><kbd>Shift + Arrows</kbd> - Move 10px</li>
                            <li><kbd>Ctrl/Cmd + S</kbd> - Save</li>
                            <li><kbd>Ctrl/Cmd + G</kbd> - Toggle grid</li>
                        </ul>
                        <h6><i class="fas fa-tools"></i> Tools</h6>
                        <ul>
                            <li><strong>Grid:</strong> 50px alignment grid</li>
                            <li><strong>Snap:</strong> Snap to grid</li>
                            <li><strong>Coords:</strong> Live coordinates</li>
                            <li><strong>Zoom:</strong> 50%-300%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#helpModal').remove();
    $('body').append(helpHTML);
    $('#helpModal').modal('show');
};

// ============================================
// HELPER FUNCTIONS
// ============================================

function collectFieldData(fieldName, defaultText = '') {
    const xValue = parseInt(document.getElementById(fieldName + 'X')?.value) || 0;
    const yValue = parseInt(document.getElementById(fieldName + 'Y')?.value) || 0;

    // Box bottom-left = text baseline position
    // Just use box coordinates directly (no offset needed)
    const adjustedX = xValue;
    const adjustedY = yValue;

    return {
        x: adjustedX,
        y: adjustedY,
        width: parseInt(document.getElementById(fieldName + 'Width')?.value) || 120,
        height: parseInt(document.getElementById(fieldName + 'Height')?.value) || 40,
        text: document.getElementById(fieldName)?.value || defaultText,
        font_size: parseInt(document.getElementById(fieldName + 'Font')?.value) || 14,
        color: document.getElementById(fieldName + 'Color')?.value || '#000000',
        show: document.getElementById(fieldName + 'Checked')?.checked || false,
        type: fieldName === 'qr_code' ? 'qr' : 'text',
        align: fieldName === 'name_student' ? (document.getElementById('name_studentCenter')?.checked ? 'center' : 'left') : 'left',
        uppercase: !!document.getElementById(fieldName + 'Uppercase')?.checked,
        bold: !!document.getElementById(fieldName + 'Bold')?.checked
    };
}

function getElementPosition(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return null;

    return {
        left: el.style.left,
        top: el.style.top,
        rect: el.getBoundingClientRect()
    };
}

function updateFieldPosition(fieldId, x, y) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    field.style.left = x + 'px';
    field.style.top = y + 'px';

    const fieldName = fieldId.replace('Div', '');
    const xInput = document.getElementById(fieldName + 'X');
    const yInput = document.getElementById(fieldName + 'Y');

    if (xInput) xInput.value = Math.round(x);
    if (yInput) yInput.value = Math.round(y);
}

// Update field style (font size, color, width, text, alignment)
function updateFieldStyle(fieldName) {
    const div = document.getElementById(fieldName + 'Div');
    if (!div) return;

    const span = document.getElementById(fieldName + 'Span');
    const width = parseInt(document.getElementById(fieldName + 'Width')?.value) || div.offsetWidth || 120;
    const height = parseInt(document.getElementById(fieldName + 'Height')?.value) || div.offsetHeight || 40;
    const fontSize = parseInt(document.getElementById(fieldName + 'Font')?.value) || 14;
    const color = document.getElementById(fieldName + 'Color')?.value || '#000000';
    const text = document.getElementById(fieldName)?.value || '';
    const uppercase = !!document.getElementById(fieldName + 'Uppercase')?.checked;
    const bold = !!document.getElementById(fieldName + 'Bold')?.checked;

    // Apply box dimensions and centering for name_student
    div.style.width = width + 'px';
    div.style.height = height + 'px';

    const content = div.querySelector('.field-content');
    if (content) {
        if (fieldName === 'name_student') {
            const center = !!document.getElementById('name_studentCenter')?.checked;
            content.style.justifyContent = center ? 'center' : 'flex-start';
            content.style.textAlign = center ? 'center' : 'left';
        } else {
            content.style.justifyContent = 'flex-start';
            content.style.textAlign = 'left';
        }
    }

    if (span) {
        span.style.fontSize = fontSize + 'px';
        span.style.color = color;
        span.style.fontWeight = bold ? '700' : '500';
        span.textContent = uppercase ? (text || '').toUpperCase() : (text || '');
    }

    updateRealTimeDisplay();
}

// Legacy handlers used by inputs (focusout)
function drawTextTFromInput() {
    const activeElement = document.getElementById('activeElement')?.value || 'name_student';
    updateFieldStyle(activeElement);
}

function updateRealTimeDisplay() {
    const activeElement = document.getElementById('activeElement')?.value;
    if (!activeElement) return;

    const field = document.getElementById(activeElement + 'Div');
    if (!field) return;

    // Display box position (equals text baseline position)
    const x = parseInt(field.style.left) || 0;
    const y = parseInt(field.style.top) || 0;

    document.getElementById('activeElementName').textContent = activeElement.replace(/_/g, ' ');
    document.getElementById('realTimeX').textContent = x;
    document.getElementById('realTimeY').textContent = y;
    document.getElementById('realTimeWidth').textContent = field.offsetWidth;
    document.getElementById('realTimeHeight').textContent = field.offsetHeight;
}

function applyZoom() {
    const certDiv = document.getElementById('certificate-div');
    if (certDiv) {
        certDiv.style.transform = `scale(${currentZoom})`;
    }

    const zoomLevel = document.getElementById('zoomLevel');
    if (zoomLevel) {
        zoomLevel.value = Math.round(currentZoom * 100) + '%';
    }
}

function showAlignmentGuide(type, position) {
    const guide = document.getElementById(type === 'horizontal' ? 'alignGuideH' : 'alignGuideV');
    if (!guide) return;

    guide.classList.add('active');
    guide.style[type === 'horizontal' ? 'top' : 'left'] = position + 'px';

    setTimeout(() => guide.classList.remove('active'), 2000);
}

function updateMousePosition(event) {
    const img = document.getElementById('image-img');
    if (!img) return;

    const imgRect = img.getBoundingClientRect();
    const x = Math.round(event.clientX - imgRect.left);
    const y = Math.round(event.clientY - imgRect.top);

    if (coordinateDisplayEnabled) {
        const tooltip = document.getElementById('coordinateTooltip');
        if (tooltip) {
            tooltip.style.left = (event.clientX + 20) + 'px';
            tooltip.style.top = (event.clientY - 30) + 'px';
            tooltip.textContent = `X: ${x}px, Y: ${y}px`;
        }
    }

    const posDiv = document.getElementById('position');
    if (posDiv) {
        posDiv.textContent = `X coords: ${x}, Y coords: ${y}`;
    }
}

function drawTextToCertificate(event) {
    const activeElement = document.getElementById('activeElement')?.value;
    if (!activeElement) return;

    const img = document.getElementById('image-img');
    if (!img) return;

    const imgRect = img.getBoundingClientRect();
    let x = event.clientX - imgRect.left;
    let y = event.clientY - imgRect.top;

    const field = document.getElementById(activeElement + 'Div');
    if (field) {
        x -= field.offsetWidth / 2;
        y -= field.offsetHeight / 2;
    }

    if (snapToGrid) {
        x = Math.round(x / gridSize) * gridSize;
        y = Math.round(y / gridSize) * gridSize;
    }

    updateFieldPosition(activeElement + 'Div', Math.round(x), Math.round(y));
    updateRealTimeDisplay();
}

// ============================================
// DRAG AND DROP IMPLEMENTATION
// ============================================

function initializeDragAndDrop() {
    $('.draggable-field').each(function() {
        makeDraggable($(this));
    });
}

function makeDraggable(element) {
    let isDragging = false;
    let startX = 0, startY = 0;
    let origLeft = 0, origTop = 0;

    element.css('touch-action', 'none');

    element.on('pointerdown', function(e) {
        if (e.button !== 0 && e.pointerType !== 'touch') return;

        isDragging = true;
        $(this).addClass('dragging');

        origLeft = parseFloat($(this).css('left')) || 0;
        origTop = parseFloat($(this).css('top')) || 0;
        startX = e.clientX;
        startY = e.clientY;

        if (this.setPointerCapture) {
            this.setPointerCapture(e.pointerId);
        }

        const fieldName = this.id.replace('Div', '');
        document.getElementById('activeElement').value = fieldName;
        updateRealTimeDisplay();
    });

    element.on('pointermove', function(e) {
        if (!isDragging) return;

        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        $(this).css('transform', `translate(${dx}px, ${dy}px)`);

        document.getElementById('realTimeX').textContent = Math.round(origLeft + dx);
        document.getElementById('realTimeY').textContent = Math.round(origTop + dy);
    });

    element.on('pointerup pointercancel', function(e) {
        if (!isDragging) return;

        isDragging = false;
        $(this).removeClass('dragging');

        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        let newLeft = origLeft + dx;
        let newTop = origTop + dy;

        if (snapToGrid) {
            newLeft = Math.round(newLeft / gridSize) * gridSize;
            newTop = Math.round(newTop / gridSize) * gridSize;
        }

        $(this).css({ left: newLeft + 'px', top: newTop + 'px', transform: '' });

        const fieldName = this.id.replace('Div', '');
        updateFieldPosition(fieldName + 'Div', newLeft, newTop);
        updateRealTimeDisplay();
    });

    element.on('click', function(e) {
        e.stopPropagation();
        $('.draggable-field').removeClass('selected active');
        $(this).addClass('selected active');

        const fieldName = this.id.replace('Div', '');
        document.getElementById('activeElement').value = fieldName;
        updateRealTimeDisplay();
    });
}

function initializePrecisionTools() {
    updateRealTimeDisplay();
}

// ============================================
// KEYBOARD SHORTCUTS
// ============================================

function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }

        const shift = e.shiftKey;
        const pixels = shift ? 10 : 1;

        switch(e.key) {
            case 'ArrowUp':
                e.preventDefault();
                nudgeField('up', pixels);
                break;
            case 'ArrowDown':
                e.preventDefault();
                nudgeField('down', pixels);
                break;
            case 'ArrowLeft':
                e.preventDefault();
                nudgeField('left', pixels);
                break;
            case 'ArrowRight':
                e.preventDefault();
                nudgeField('right', pixels);
                break;
            case 'g':
            case 'G':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    toggleGrid();
                }
                break;
            case 's':
            case 'S':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    saveCertificateData();
                }
                break;
        }
    });
}

// ============================================
// TEMPLATE DATA LOADING
// ============================================

async function loadExistingTemplateData() {
    try {
        const response = await fetch('/certifs/{{ $certif->id }}/template-data');
        const result = await response.json();

        if (result.success && result.template_data) {
            Object.keys(result.template_data).forEach(fieldName => {
                const fieldData = result.template_data[fieldName];

                const xInput = document.getElementById(fieldName + 'X');
                const yInput = document.getElementById(fieldName + 'Y');
                const widthInput = document.getElementById(fieldName + 'Width');
                const heightInput = document.getElementById(fieldName + 'Height');
                const fontInput = document.getElementById(fieldName + 'Font');
                const colorInput = document.getElementById(fieldName + 'Color');
                const checkedInput = document.getElementById(fieldName + 'Checked');
                const textInput = document.getElementById(fieldName);

                // Box positioned directly at stored coordinates
                const displayX = fieldData.x || 0;
                const displayY = fieldData.y || 0;

                if (xInput) xInput.value = displayX;
                if (yInput) yInput.value = displayY;
                if (widthInput) widthInput.value = fieldData.width || 100;
                if (heightInput) heightInput.value = fieldData.height || 30;
                if (fontInput) fontInput.value = fieldData.font_size || 14;
                if (colorInput) colorInput.value = fieldData.color || '#000000';
                if (checkedInput) checkedInput.checked = fieldData.show || false;
                if (textInput && fieldData.text) textInput.value = fieldData.text;

                if (fieldData.show) {
                    const field = document.getElementById(fieldName + 'Div');
                    if (field) {
                        field.style.left = displayX + 'px';
                        field.style.top = displayY + 'px';
                        field.style.display = 'block';
                        const w = fieldData.width || 120;
                        const h = fieldData.height || 40;
                        field.style.width = w + 'px';
                        field.style.height = h + 'px';
                        // Reflect styles
                        updateFieldStyle(fieldName);
                    }
                }
            });
        }
    } catch (error) {
        console.error('❌ Error loading template data:', error);
    }
}

function checkAndInitializeTemplate() {
}

// ============================================
// DOCUMENT READY
// ============================================

$(function() {
    // Initialize color pickers
    $('#name_studentColor').colorpicker();
    $('#dateColor').colorpicker();
    $('#qr_codeColor').colorpicker();
    $('#serial_numberColor').colorpicker();

    // Toggle sidebar
    $(".sidebar-toggle").trigger("click");

    // Initialize drag and drop
    initializeDragAndDrop();

    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();

    // Load existing data
    loadExistingTemplateData();

    // Initialize precision tools
    initializePrecisionTools();

    // Initialize real-time display
    updateRealTimeDisplay();

    // Add coordinate tooltip
    $('body').append('<div class="coordinate-tooltip" id="coordinateTooltip"></div>');

    // Attach event listeners
    const certDiv = document.getElementById('certificate-div');
    if (certDiv) {
        certDiv.addEventListener('mousemove', updateMousePosition);
        certDiv.addEventListener('click', drawTextToCertificate);
    }

    // Check template after delay
    setTimeout(checkAndInitializeTemplate, 1000);
});

</script>
@endpush
