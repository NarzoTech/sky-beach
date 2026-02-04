@php
    $header_admin = Auth::guard('admin')->user();
@endphp
<!DOCTYPE html>

<html lang="en"
    class="light-style layout-menu-fixed layout-compact layout-navbar-fixed {{ isRoute(['admin.pos'], 'layout-menu-collapsed') }}"
    dir="ltr" data-theme="theme-default" data-style="light" data-assets-path="{{ asset('backend/assets') }}/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Sky Beach') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->favicon) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    @include('admin.layouts.styles')

    @stack('css')


    <style>
        .template-customizer-open-btn {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            @include('admin.layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                @include('admin.layouts.nav')

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->

                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    @if (!isRoute('admin.pos*'))
                        <!-- Footer -->
                        @include('admin.layouts.footer')
                    @endif
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <x-admin.delete-modal />

    <!-- Calculator Modal (Global) -->
    <div class="modal fade calculator-modal" id="calculatorModal" tabindex="-1" aria-labelledby="calculatorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="calculatorModalLabel">
                        <i class="bx bx-calculator me-2"></i>{{ __('Calculator') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="calculator-display mb-3" id="calcDisplay">0</div>
                    <div class="row g-2">
                        <div class="col-3">
                            <button class="calc-btn calc-btn-clear" onclick="clearCalc()">C</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc('(')">(</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc(')')">)</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc('/')">/</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('7')">7</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('8')">8</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('9')">9</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc('*')">x</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('4')">4</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('5')">5</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('6')">6</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc('-')">-</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('1')">1</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('2')">2</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('3')">3</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-operator" onclick="appendCalc('+')">+</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('0')">0</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('00')">00</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-number" onclick="appendCalc('.')">.</button>
                        </div>
                        <div class="col-3">
                            <button class="calc-btn calc-btn-equals" onclick="calculateResult()">=</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    @include('admin.layouts.javascripts')

    <!-- Global Calculator Scripts -->
    <script>
        // Calculator Functions
        let calcExpression = '';

        function appendCalc(value) {
            if (calcExpression === '0' || calcExpression === 'Error') {
                calcExpression = '';
            }
            calcExpression += value;
            document.getElementById('calcDisplay').textContent = calcExpression || '0';
        }

        function clearCalc() {
            calcExpression = '';
            document.getElementById('calcDisplay').textContent = '0';
        }

        function calculateResult() {
            try {
                const result = eval(calcExpression);
                calcExpression = String(result);
                document.getElementById('calcDisplay').textContent = result.toLocaleString();
            } catch (e) {
                document.getElementById('calcDisplay').textContent = 'Error';
                calcExpression = '';
            }
        }

        // Keyboard support for calculator
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('calculatorModal');
            if (modal && modal.classList.contains('show')) {
                if (e.key >= '0' && e.key <= '9') {
                    appendCalc(e.key);
                } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
                    appendCalc(e.key);
                } else if (e.key === '.') {
                    appendCalc('.');
                } else if (e.key === 'Enter' || e.key === '=') {
                    e.preventDefault();
                    calculateResult();
                } else if (e.key === 'Escape' || e.key === 'c' || e.key === 'C') {
                    clearCalc();
                } else if (e.key === 'Backspace') {
                    calcExpression = calcExpression.slice(0, -1);
                    document.getElementById('calcDisplay').textContent = calcExpression || '0';
                }
            }
        });
    </script>



    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    @stack('scripts')

</body>

</html>
