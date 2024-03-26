<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --tblr-font-sans-serif: 'Inter';
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
</head>

<body>
    <div class="page">
        <header class="navbar navbar-expand-sm navbar-light d-print-none">
            <div class="container-xl">
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="#">
                        <img src="/logo.png" width="110" height="32" alt="Tabler" class="navbar-brand-image" />
                        <span class="px-2">
                            Laravel Claude Chatbot
                        </span>
                    </a>
                </h1>

                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0">
                            <span class="avatar avatar-circle avatar-sm" style="background-image: url(https://ahmadrosid.com/profile.png)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>Ahmad Rosid</div>
                                <div class="mt-1 small text-secondary">Software Engineer</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <div class="page-wrapper">
            <div class="page-body">
                @yield("content")
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>