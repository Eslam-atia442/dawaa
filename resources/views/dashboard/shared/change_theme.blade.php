<script>
    $(document).on('click', '.change-theme', function () {
      localStorage.setItem("theme", $(this).data('theme'));
      getTheme();
    });

    window.addEventListener("load", getTheme);

    function getTheme() {
      var theme = localStorage.getItem("theme");
      if (theme == 'dark') {
        document.getElementById("theme-core").setAttribute("href", "{{ asset('admin/css/rtl/core-dark.css') }}");
        document.getElementById("theme-default").setAttribute("href", "{{ asset('admin/css/rtl/theme-default-dark.css') }}");
        document.getElementById("theme-icon").setAttribute("class", "ti ti-md ti-moon");
        $("#main_html").removeClass('light-style').addClass('dark-style');
      } else {
        document.getElementById("theme-core").setAttribute("href", "{{ asset('admin/css/rtl/core.css') }}");
        document.getElementById("theme-default").setAttribute("href", "{{ asset('admin/css/rtl/theme-default.css') }}");
        document.getElementById("theme-icon").setAttribute("class", "ti ti-md ti-sun");
        $("#main_html").removeClass('dark-style').addClass('light-style');
      }
    }

</script>
