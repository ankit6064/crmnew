$(function () {
    "use strict";
    $(function () {
        $(".preloader").fadeOut();
    }),
        jQuery(document).on("click", ".mega-dropdown", function (i) {
            i.stopPropagation();
        });
    var i = function () {
        (window.innerWidth > 0 ? window.innerWidth : this.screen.width) < 1170
            ? ($("body").addClass("mini-sidebar"), $(".navbar-brand span").hide(), $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible"), $(".sidebartoggler i").addClass("ti-menu"))
            : ($("body").removeClass("mini-sidebar"), $(".navbar-brand span").show());
        var i = (window.innerHeight > 0 ? window.innerHeight : this.screen.height) - 1;
        (i -= 70) < 1 && (i = 1), i > 70 && $(".page-wrapper").css("min-height", i + "px");
    };
    $(window).ready(i),
        $(window).on("resize", i),
        $(".sidebartoggler").on("click", function () {
            $("body").hasClass("mini-sidebar")
                ? ($("body").trigger("resize"), $(".scroll-sidebar, .slimScrollDiv").css("overflow", "hidden").parent().css("overflow", "visible"), $("body").removeClass("mini-sidebar"), $(".navbar-brand span").show())
                : ($("body").trigger("resize"), $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible"), $("body").addClass("mini-sidebar"), $(".navbar-brand span").hide());
        }),
        // $(".fix-header .topbar").stick_in_parent({}),
        $(".nav-toggler").click(function () {
            $("body").toggleClass("show-sidebar"), $(".nav-toggler i").toggleClass("mdi mdi-menu"), $(".nav-toggler i").addClass("mdi mdi-close");
        }),
        $(".search-box a, .search-box .app-search .srh-btn").on("click", function () {
            $(".app-search").toggle(200);
        }),
        $(".right-side-toggle").click(function () {
            $(".right-sidebar").slideDown(50), $(".right-sidebar").toggleClass("shw-rside");
        }),
        $(".floating-labels .form-control")
            .on("focus blur", function (i) {
                $(this)
                    .parents(".form-group")
                    .toggleClass("focused", "focus" === i.type || this.value.length > 0);
            })
            .trigger("blur"),
        $(function () {
            for (
                var i = window.location,
                    o = $("ul#sidebarnav a")
                        .filter(function () {
                            return this.href == i;
                        })
                        .addClass("active")
                        .parent()
                        .addClass("active");
                o.is("li");

            )
                o = o.parent().addClass("in").parent().addClass("active");
        }),
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        }),
        $(function () {
            $('[data-toggle="popover"]').popover();
        }),
        // $(function () {
        //     $("#sidebarnav").metisMenu();
        // }),
        // $(".scroll-sidebar").slimScroll({ position: "left", size: "5px", height: "100%", color: "#dcdcdc" }),
        // $(".message-center").slimScroll({ position: "right", size: "5px", color: "#dcdcdc" }),
        // $(".aboutscroll").slimScroll({ position: "right", size: "5px", height: "80", color: "#dcdcdc" }),
        // $(".message-scroll").slimScroll({ position: "right", size: "5px", height: "570", color: "#dcdcdc" }),
        // $(".chat-box").slimScroll({ position: "right", size: "5px", height: "470", color: "#dcdcdc" }),
        // $(".slimscrollright").slimScroll({ height: "100%", position: "right", size: "5px", color: "#dcdcdc" }),
        $("body").trigger("resize"),
        $(".list-task li label").click(function () {
            $(this).toggleClass("task-done");
        }),
        $("#to-recover").on("click", function () {
            $("#loginform").slideUp(), $("#recoverform").fadeIn();
        }),
        $('a[data-action="collapse"]').on("click", function (i) {
            i.preventDefault(), $(this).closest(".card").find('[data-action="collapse"] i').toggleClass("ti-minus ti-plus"), $(this).closest(".card").children(".card-body").collapse("toggle");
        }),
        $('a[data-action="expand"]').on("click", function (i) {
            i.preventDefault(), $(this).closest(".card").find('[data-action="expand"] i').toggleClass("mdi-arrow-expand mdi-arrow-compress"), $(this).closest(".card").toggleClass("card-fullscreen");
        }),
        $('a[data-action="close"]').on("click", function () {
            $(this).closest(".card").removeClass().slideUp("fast");
        }),
        $(".custom-file-input").on("change", function () {
            var i = $(this).val();
            $(this).next(".custom-file-label").html(i);
        });

    // Intercept DataTables search inputs globally to only trigger search after 2+ characters
    $(document).on('init.dt', function (e, settings) {
        var api = new $.fn.dataTable.Api(settings);
        var $input = $('div.dataTables_filter input', api.table().container());
        
        // Unbind the default instant search handler
        $input.off('keyup.DT search.DT input.DT paste.DT cut.DT');
        
        // Bind the custom 2+ characters search handler
        $input.on('keyup.DT search.DT input.DT paste.DT cut.DT', function (event) {
            var val = this.value;
            var currentSearch = api.search();
            var searchTriggered = false;
            
            if (val.length >= 2) {
                if (val !== currentSearch) {
                    api.search(val).draw();
                    searchTriggered = true;
                }
            } else {
                // If search is cleared or less than 2 chars, reset search if there was a previous search active
                if (currentSearch !== '') {
                    api.search('').draw();
                    searchTriggered = true;
                }
            }
            
            // Prevent event from bubbling up to document keyup handlers (like showing spinner overlay)
            // if we didn't actually trigger a new search/draw.
            if (!searchTriggered) {
                event.stopPropagation();
            }
        });

        // Also handle custom #global_filter inputs if they exist
        var $globalFilter = $('#global_filter');
        if ($globalFilter.length) {
            var ns = '.globalFilter_' + settings.sTableId;
            $globalFilter.off('keyup' + ns + ' keypress' + ns + ' input' + ns + ' paste' + ns + ' cut' + ns);
            var lastGlobalSearch = $globalFilter.val() || '';
            $globalFilter.on('keyup' + ns + ' keypress' + ns + ' input' + ns + ' paste' + ns + ' cut' + ns, function (event) {
                // Ignore enter key in keypress to prevent form submission if wrapped in form
                if (event.type === 'keypress' && event.which === 13) {
                    event.preventDefault();
                }
                var val = this.value;
                if (val.length >= 2) {
                    if (val !== lastGlobalSearch) {
                        lastGlobalSearch = val;
                        api.ajax.reload();
                    }
                } else {
                    if (lastGlobalSearch !== '') {
                        lastGlobalSearch = '';
                        api.ajax.reload();
                    }
                }
            });
        }
    });
});
