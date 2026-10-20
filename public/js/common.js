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

    // Set default DataTables pageLength to 25 globally
    if (window.jQuery && $.fn && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Global handler to add "Go to page" jump controls to any DataTable pagination
        $(document).on('draw.dt', function (e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var tableContainer = api.table().container();
            var $paginate = $('div.dataTables_paginate', tableContainer);

            if (!$paginate.length) return;

            var pageInfo = api.page.info();
            if (!pageInfo) return;

            var currentPage = pageInfo.page + 1;
            var totalPages = pageInfo.pages || 1;

            var $jumpWrap = $paginate.find('.dt-page-jump-wrap');
            if ($jumpWrap.length) {
                $jumpWrap.find('.dt-page-jump-input').attr('max', totalPages).val(currentPage);
                $jumpWrap.find('.dt-page-total-label').text('of ' + totalPages);
                return;
            }

            var jumpHtml = '<div class="dt-page-jump-wrap" style="display:inline-flex;align-items:center;margin-left:14px;gap:6px;vertical-align:middle;">' +
                '<span style="font-size:13px;font-weight:500;color:#495057;">Go to page:</span>' +
                '<input type="number" min="1" max="' + totalPages + '" value="' + currentPage + '" class="form-control form-control-sm dt-page-jump-input" style="width:65px;height:32px;display:inline-block;text-align:center;font-size:13px;padding:2px 6px;border:1px solid #ced4da;border-radius:4px;" placeholder="' + currentPage + '">' +
                '<span class="dt-page-total-label" style="font-size:12px;color:#6c757d;">of ' + totalPages + '</span>' +
                '<button type="button" class="btn btn-sm dt-page-jump-btn" style="background-color:#0d3a6b;color:#fff;border:none;height:32px;padding:0 12px;font-size:12px;font-weight:600;border-radius:4px;cursor:pointer;">Go</button>' +
                '</div>';

            $paginate.append(jumpHtml);

            $paginate.off('click.dtJump', '.dt-page-jump-btn').on('click.dtJump', '.dt-page-jump-btn', function (event) {
                event.preventDefault();
                jump();
            });

            $paginate.off('keypress.dtJump', '.dt-page-jump-input').on('keypress.dtJump', '.dt-page-jump-input', function (event) {
                if (event.which === 13) {
                    event.preventDefault();
                    jump();
                }
            });

            function jump() {
                var inputVal = $paginate.find('.dt-page-jump-input').val();
                var targetPage = parseInt(inputVal, 10);
                if (isNaN(targetPage) || targetPage < 1) {
                    targetPage = 1;
                } else if (targetPage > totalPages) {
                    targetPage = totalPages;
                }
                $paginate.find('.dt-page-jump-input').val(targetPage);
                if (targetPage !== currentPage) {
                    api.page(targetPage - 1).draw('page');
                }
            }
        });
    }
});

window.handleParentCategoryChange = function (parentSelectEl, childSelectId, onchangeCallback) {
    var parentVal = $(parentSelectEl).val();
    var targetChildId = childSelectId || 'reminder_for';
    var $childSelect = $('#' + targetChildId);
    var $childCol = $('#' + targetChildId + '_child_col');
    if (!$childCol.length) {
        $childCol = $childSelect.closest('.child-col, .col-md-6, .col-6');
    }
    var $parentCol = $(parentSelectEl).closest('.parent-col, .col-md-6, .col-6, .col-12');

    $childSelect.empty();

    if (parentVal === 'Follow up') {
        $childSelect.append('<option value="Follow-up Call" selected>Call</option>');
        $childSelect.append('<option value="Follow-up Email/Info Requested">Email</option>');
        $childSelect.append('<option value="Follow-up Text">Text</option>');
        $childCol.show();
        if ($parentCol.length) {
            $parentCol.removeClass('col-12').addClass('col-md-6 col-6');
        }
    } else if (parentVal === 'Declined') {
        $childSelect.append('<option value="No Opportunity" selected>No Opportunity</option>');
        $childSelect.append('<option value="DNC">DNC</option>');
        $childSelect.append('<option value="Not Right Party">Not Right Party</option>');
        $childSelect.append('<option value="Left the Organization">Left the Organization</option>');
        $childSelect.append('<option value="Reference Shared">Reference Shared</option>');
        $childCol.show();
        if ($parentCol.length) {
            $parentCol.removeClass('col-12').addClass('col-md-6 col-6');
        }
    } else if (parentVal === 'Meeting Setup' || parentVal === 'Meeting Set-up') {
        $childSelect.append('<option value="Meeting Set-up" selected>Meeting Setup</option>');
        $childCol.hide();
        if ($parentCol.length) {
            $parentCol.removeClass('col-md-6 col-6').addClass('col-12');
        }
    } else {
        $childSelect.append('<option value="">Choose Option</option>');
        $childCol.hide();
        if ($parentCol.length) {
            $parentCol.removeClass('col-md-6 col-6').addClass('col-12');
        }
    }

    if (typeof onchangeCallback === 'function') {
        onchangeCallback();
    }
};

window.setNoteOptionsValue = function (reminderForVal, parentSelectId, childSelectId, onchangeCallback) {
    var $parentSelect = $('#' + (parentSelectId || 'parent_reminder_for'));
    var parentCategory = '';

    if (['Follow-up Call', 'CALL', 'Call', 'Follow-up Email/Info Requested', 'EMAIL', 'Email', 'Follow-up Text', 'TEXT', 'Text', 'Callback'].indexOf(reminderForVal) !== -1) {
        parentCategory = 'Follow up';
    } else if (['No Opportunity', 'NO', 'DNC', 'DND', 'Not Right Party', 'NGP', 'Left the Organization', 'LTO', 'Reference Shared', 'RS', 'Rs', 'Declined'].indexOf(reminderForVal) !== -1) {
        parentCategory = 'Declined';
    } else if (reminderForVal === 'Meeting Set-up' || reminderForVal === 'Meeting Setup' || reminderForVal === 'MSUP') {
        parentCategory = 'Meeting Setup';
    }

    $parentSelect.val(parentCategory);
    window.handleParentCategoryChange($parentSelect[0], childSelectId || 'reminder_for', onchangeCallback);
    if (reminderForVal) {
        $('#' + (childSelectId || 'reminder_for')).val(reminderForVal);
        if (typeof onchangeCallback === 'function') {
            onchangeCallback();
        }
    }
};

