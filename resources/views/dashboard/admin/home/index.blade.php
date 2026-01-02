@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.home')}}
@endsection

@push('css_files')
<style>
    .stats-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 150px;
    }

    .stats-card .card-body {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        font-size: 3rem;
        opacity: 0.8;
    }

    .count-animation {
        font-size: 2rem;
        font-weight: bold;
        opacity: 0;
        animation: countUp 0.8s ease-out forwards;
    }

    .stats-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }

    .badge-count {
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    .badge-area {
        min-height: 30px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .clickable-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    a.text-decoration-none:hover {
        text-decoration: none !important;
    }

    a.text-decoration-none:hover .stats-card {
        color: inherit;
    }

    @keyframes countUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white stats-card">
                <div class="card-body text-center py-4">
                    <h2 class="mb-0 text-white">@lang('trans.home') @lang('trans.dashboard')</h2>
                    <p class="mb-0 opacity-75"> @lang('trans.management_statistics')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @foreach($statistics as $key => $stat)
            @can($stat['permission'])
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="{{ route($stat['route']) }}" class="text-decoration-none">
                        <div class="card stats-card bg-{{ $stat['color'] }} {{ $stat['text_color'] }} fade-in clickable-card" style="animation-delay: {{ $stat['delay'] }};">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="stats-label text-white-50">@lang($stat['title'])</h6>
                                        <div class="count-animation {{ $stat['text_color'] }}" data-target="{{ $stat['total'] }}">0</div>

                                        <div class="badge-area mt-2">
                                            @if(isset($stat['active']))
                                                <span class="{{ $stat['badge_color'] }} badge-count me-1">
                                                    @lang('trans.active'): {{ $stat['active'] }}
                                                </span>
                                            @endif
                                            @if(isset($stat['inactive']))
                                                <span class="{{ $stat['badge_color'] }} badge-count">
                                                    @lang('trans.inactive'): {{ $stat['inactive'] }}
                                                </span>
                                            @endif
                                            @if(isset($stat['verified']))
                                                <span class="{{ $stat['badge_color'] }} badge-count me-1">
                                                    @lang('trans.verified'): {{ $stat['verified'] }}
                                                </span>
                                            @endif
                                            @if(isset($stat['unverified']))
                                                <span class="{{ $stat['badge_color'] }} badge-count">
                                                    @lang('trans.unverified'): {{ $stat['unverified'] }}
                                                </span>
                                            @endif
                                            @if(isset($stat['recent']))
                                                <span class="{{ $stat['badge_color'] }} badge-count">
                                                    @lang('trans.recent'): {{ $stat['recent'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="stats-icon text-white-50">
                                        <i class="{{ $stat['icon'] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
        @endforeach
    </div>
</div>
@endsection

@push('js_files')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate counter numbers
    function animateCounter(element, target, duration = 1000) {
        const start = 0;
        const increment = target / (duration / 16); // 60fps
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 16);
    }

    // Initialize counter animations with staggered delays
    const counters = document.querySelectorAll('.count-animation');
    counters.forEach((counter, index) => {
        const target = parseInt(counter.getAttribute('data-target'));

        // Start animation after card fade-in completes
        setTimeout(() => {
            animateCounter(counter, target, 1500);
        }, 100 + (index * 100));
    });

    // Add enhanced hover effects to clickable cards
    const clickableCards = document.querySelectorAll('.clickable-card');
    clickableCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.2)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });
});
</script>
 @endpush
