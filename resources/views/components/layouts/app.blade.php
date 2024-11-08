<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
        {{-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <title>{{ config('app.name', 'Jodoin') }} {{ isset($title) ? '| '.$title : '' }}</title>
    </head>
    <body>
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                            <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script>
            Livewire.on("swal-fired", (params) => {
                const { title, message, type, footer = null } = params[0];

                Swal.fire({
                    title: title,
                    text: message,
                    icon: type,
                    confirmButtonText: "Ok",
                    footer: footer
                });
            });

            document.addEventListener("confirmation-fired", function (event) {
                const { eventName, rowId = null, title = "Yakin ?", message =  "Tekan Ya jika Anda sudah yakin."} = event.detail

                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(eventName, { id: rowId });
                    }
                });
            });

            document.addEventListener("info-fired", function (event) {
                const { title = "Infromasi Penting", message =  ""} = event.detail

                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ya",
                })
            });

            Livewire.on("toast-fired", (params) => {
                const { title, icon } = params[0];

                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });

                Toast.fire({
                    icon: icon,
                    title: title,
                });
            });
        </script>

        @stack('scripts')
    </body>
</html>
