@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<div class="pets-page">
    <div class="container">
        <div class="pets-layout">

        <!-- Sidebar / Filter Panel -->
        <aside class="pets-sidebar">
            <div class="pets-filter-panel">
                <form id="filterForm" action="{{ route('client.accessories.index') }}" method="GET">
                    
                    <input type="hidden" name="category" value="{{ request('category') }}">

                    <div class="pets-search-wrapper">
                        <input type="text"
                            name="search"
                            placeholder="Search accessories..."
                            value="{{ request('search') }}"
                            class="pets-search-input"
                            autocomplete="off">

                        <button type="submit" class="pets-search-button">
                            🔍
                        </button>
                    </div>

                    <!-- Categories -->
                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Categories</span>
                        </h4>
                        <ul class="pets-breed-list">
                            <li class="pets-breed-item">
                                <a href="{{ route('client.accessories.index', request()->except(['category', 'page'])) }}"
                                   class="{{ !request('category') ? 'active' : '' }}">
                                   All Categories
                                </a>
                            </li>
                            @foreach($categories as $category)
                                <li class="pets-breed-item">
                                    <a href="{{ route('client.accessories.index', array_merge(request()->query(), ['category' => $category->Category])) }}"
                                       class="{{ request('category') === $category->Category ? 'active' : '' }}">
                                       {{ $category->Category }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Outlet</span>
                        </h4>
                        <select name="outlet" onchange="this.form.submit()" class="pets-select">
                            <option value="">All Outlets</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->City }}"
                                    {{ request('outlet') === $outlet->City ? 'selected' : '' }}>
                                    {{ $outlet->City }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>
        </aside>

        <!-- Main Content (Grid) -->
        <main class="pets-main">

            <!-- Breadcrumbs -->
            <div class="pets-breadcrumbs">
                <a href="{{ route('client.home') }}">Home</a>
                <span>/</span>
                <span class="active">Browse Accessories</span>
                
                @if(request('category'))
                    <span>/</span> <span class="text-brand-medium">{{ request('category') }}</span>
                @endif
                @if(request('outlet'))
                    <span>/</span> <span class="text-brand-medium">{{ request('outlet') }}</span>
                @endif
            </div>

            <!-- Header -->
            <div class="pets-header">
                <div class="pets-header-title">
                    <h2>Pet Accessories</h2>
                    <span>{{ $accessories->total() }} results found</span>
                </div>
            </div>

            @if($accessories->count() > 0)
                <div class="pets-grid">
                    @foreach($accessories as $accessory)
                        <div class="pets-card">
                            <div class="pets-card-image">
                                <a href="#">
                                    <img src="{{ asset($accessory->ImageURL1 ?: 'image/default-pet.png') }}"
                                         alt="{{ $accessory->AccessoryName }}">
                                </a>

                                <div class="pets-card-badge">{{ $accessory->Category }}</div>
                            </div>

                            <div class="pets-card-content">
                                <h3>{{ $accessory->AccessoryName }}</h3>
                                <p class="pets-card-price"><span class="pets-price-label">From </span>RM {{ number_format($accessory->min_price, 2) }}</p>

                                <a href="{{ route('client.accessories.show', $accessory->AccessoryID) }}" class="pets-card-button">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pets-pagination">
                    {{ $accessories->links() }}
                </div>
            @else
                <div class="pets-empty">
                    <p>No accessories found matching your criteria.</p>
                    <a href="{{ route('client.accessories.index') }}">Clear all filters</a>
                </div>
            @endif

        </main>
    </div>
</div>

@endsection
