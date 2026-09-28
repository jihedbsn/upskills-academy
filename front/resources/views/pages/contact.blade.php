@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-4">Contact Us</h1>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Get in Touch</h5>
                            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                            <div class="mb-3">
                                <i class="bi bi-envelope me-2"></i>
                                <span>contact@example.com</span>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-telephone me-2"></i>
                                <span>+1 (555) 123-4567</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
