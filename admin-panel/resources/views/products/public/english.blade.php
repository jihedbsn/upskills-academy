@extends('layouts.app')

@section('content')
<div class="app-content content ecommerce-application">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            <i data-feather="align-left" class="me-2"></i>
                            Public Content - English Section
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Public Content English</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="grid"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('products.create') }}">
                                <i data-feather="plus" class="me-1"></i>
                                New product
                            </a>
                            <a class="dropdown-item" href="{{ route('products.public.arabic') }}">
                                <i data-feather="align-right" class="me-1"></i>
                                Arabic Section
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Products Grid -->
            <section id="card-demo-example">
                <div class="row match-height">
                    @forelse($products as $product)
                        @php
                            $englishVariation = $product->variations->where('langue', 'en')->first();
                        @endphp
                        @if($englishVariation)
                            <div class="col-md-6 col-lg-4">
                                <div class="card ecommerce-card">
                                    <div class="card-header">
                                        <div class="item-img">
                                            <a href="{{ route('products.show', $product) }}">
                                                <div class="bg-light-secondary rounded p-2 text-center">
                                                    <i data-feather="package" class="text-primary" style="width: 48px; height: 48px;"></i>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="item-options text-end">
                                            <div class="dropdown">
                                                <button class="btn-options" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('products.show', $product) }}">
                                                        <i data-feather="eye" class="me-1"></i>
                                                        Voir
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('products.edit', $product) }}">
                                                        <i data-feather="edit" class="me-1"></i>
                                                        Modifier
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-wrapper">
                                            <div class="item-rating">
                                                <div class="badge rounded-pill bg-light-info">
                                                    <i data-feather="flag" class="text-info me-1"></i>
                                                    Anglais
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="item-price text-center fw-bolder">
                                                    {{ $englishVariation->name }}
                                                </h6>
                                            </div>
                                            <div>
                                                <p class="item-description">
                                                    {!! Str::limit(strip_tags($englishVariation->short_description), 100) !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-options text-center">
                                        <div class="item-wrapper">
                                            <div class="item-cost">
                                                <h4 class="item-price">
                                                    @if($product->prix)
                                                        ${{ number_format($product->prix, 2) }}
                                                    @else
                                                        <span class="text-muted">Prix non défini</span>
                                                    @endif
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="text-start">
                                                <small class="text-muted">
                                                    <i data-feather="tag" class="me-1"></i>
                                                    {{ $product->category->titre ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    <i data-feather="user" class="me-1"></i>
                                                    {{ $product->teacher->nom ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i data-feather="package" class="text-muted" style="width: 64px; height: 64px;"></i>
                                    </div>
                                    <h4 class="text-muted">Aucun produit en anglais trouvé</h4>
                                    <p class="text-muted">Commencez par ajouter des produits avec du contenu anglais.</p>
                                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                                        <i data-feather="plus" class="me-1"></i>
                                        Ajouter un produit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-slug generation for English content
    document.addEventListener('DOMContentLoaded', function() {
        const englishNameInput = document.querySelector('input[name="english_name"]');
        const englishSlugInput = document.querySelector('input[name="english_slug"]');
        
        if (englishNameInput && englishSlugInput) {
            englishNameInput.addEventListener('input', function() {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                englishSlugInput.value = slug;
            });
        }
    });
</script>
@endpush
