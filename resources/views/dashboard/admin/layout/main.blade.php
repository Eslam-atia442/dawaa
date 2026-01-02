@include('dashboard.admin.layout.partial.head')

<body>


<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar" id="app">

    <div class="layout-container">
        @include('dashboard.admin.layout.partial.sidebar')
        <!-- Layout container -->
        <div class="layout-page">
            @include('dashboard.admin.layout.partial.navbar')
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')


                    <!-- / Content -->
                </div>
                @include('dashboard.admin.layout.partial.footer')
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

@include('dashboard.admin.layout.partial.script')



</body>
</html>
