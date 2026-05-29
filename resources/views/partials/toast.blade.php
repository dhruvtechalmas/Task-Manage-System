@php
    $toastMessages = [];

    if (session('status')) {
        $toastMessages[] = [
            'type' => 'success',
            'message' => session('status'),
        ];
    }

    if (session('error')) {
        $toastMessages[] = [
            'type' => 'error',
            'message' => session('error'),
        ];
    }

    foreach ($errors->all() as $message) {
        $toastMessages[] = [
            'type' => 'error',
            'message' => $message,
        ];
    }
@endphp

@if ($toastMessages !== [])
    <div id="toastMessages" class="fixed right-5 top-5 z-[9999] w-full max-w-sm space-y-3">
        @foreach ($toastMessages as $toast)
            <div class="rounded-lg border px-4 py-3 text-sm font-medium shadow-lg {{ $toast['type'] === 'success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' }}">
                {{ $toast['message'] }}
            </div>
        @endforeach
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('toastMessages')?.remove();
        }, 3500);
    </script>
@endif
