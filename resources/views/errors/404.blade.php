@extends('layouts.app')

@section('content')
<main class="w-full h-[calc(100vh-80px)] bg-bg-content text-text-content flex items-center justify-center px-6">
    <div class="text-center max-w-md mx-auto flex flex-col items-center">
        
        <!-- Icon -->
        <i class="ph ph-sparkles text-6xl text-gold-accent/40 mb-6"></i>
        
        <!-- Subtitle -->
        <span class="text-[10px] uppercase tracking-[0.25em] text-gold-accent font-sans mb-2 font-medium">Not Found</span>
        
        <!-- Title -->
        <h1 class="font-serif text-3xl text-text-content mb-4 font-semibold">
            Piece not found
        </h1>
        
        <!-- Description -->
        <p class="text-xs font-sans text-muted-content leading-relaxed mb-8">
            The specific artisan creation you are looking for does not exist in our room or has drifted away.
        </p>
        
        <!-- Return Link -->
        <a href="{{ route('shop.index') }}" class="px-8 py-3.5 border border-gold-accent text-gold-accent hover:bg-gold-accent hover:text-[#0d1410] font-sans font-medium rounded-sm transition-colors duration-300 tracking-wider text-xs uppercase cursor-pointer">
            Return to Collections
        </a>
    </div>
</main>
@endsection
