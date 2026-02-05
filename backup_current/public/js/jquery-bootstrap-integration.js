/**
 * jQuery + Bootstrap Integration Framework
 * Kolaborasi optimal antara jQuery dan Bootstrap untuk aplikasi Koperasi
 */

(function($) {
    'use strict';

    // Global jQuery + Bootstrap Configuration
    window.KSP = {
        version: '1.0.0',
        config: {
            animationDuration: 300,
            debounceDelay: 300,
            loadingText: 'Memuat...',
            errorText: 'Terjadi kesalahan',
            successText: 'Berhasil',
            confirmText: 'Apakah Anda yakin?'
        },
        
        // Initialize all components
        init: function() {
            this.setupjQuery();
            this.setupBootstrap();
            this.setupIntegrations();
            this.setupMobileOptimizations();
            this.setupEventHandlers();
            this.setupAjaxDefaults();
            
            console.log('Framework KSP diinisialisasi');
        },
        
        // jQuery Setup and Extensions
        setupjQuery: function() {
            // jQuery AJAX defaults
            $.ajaxSetup({
                timeout: 30000,
                cache: false,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    KSP.showLoading();
                },
                complete: function() {
                    KSP.hideLoading();
                },
                error: function(xhr, status, error) {
                    KSP.handleAjaxError(xhr, status, error);
                }
            });
            
            // Custom jQuery methods
            $.fn.extend({
                // Auto-format form inputs
                autoFormat: function() {
                    return this.each(function() {
                        var $this = $(this);
                        if ($this.hasClass('currency')) {
                            $this.on('input', KSP.formatCurrency);
                        } else if ($this.hasClass('phone')) {
                            $this.on('input', KSP.formatPhone);
                        } else if ($this.hasClass('uppercase')) {
                            $this.on('input', function() {
                                $(this).val($(this).val().toUpperCase());
                            });
                        }
                    });
                },
                
                // Auto-resize textarea
                autoResize: function() {
                    return this.each(function() {
                        var $this = $(this);
                        $this.on('input', function() {
                            this.style.height = 'auto';
                            this.style.height = (this.scrollHeight) + 'px';
                        });
                    });
                },
                
                // Bootstrap modal with jQuery enhancement
                modalEnhanced: function(options) {
                    var $modal = $(this);
                    
                    // Add backdrop click prevention if specified
                    if (options && options.preventBackdropClose) {
                        $modal.attr('data-backdrop', 'static');
                        $modal.attr('data-keyboard', 'false');
                    }
                    
                    // Show with animation
                    $modal.on('show.bs.modal', function() {
                        $('body').addClass('modal-open');
                    });
                    
                    $modal.on('hidden.bs.modal', function() {
                        $('body').removeClass('modal-open');
                    });
                    
                    return $modal.modal(options);
                },
                
                // Enhanced table with jQuery + Bootstrap
                tableEnhanced: function(options) {
                    var $table = $(this);
                    
                    // Add Bootstrap table classes
                    $table.addClass('table table-hover table-striped');
                    
                    // Add responsive wrapper
                    if (!$table.parent().hasClass('table-responsive')) {
                        $table.wrap('<div class="table-responsive"></div>');
                    }
                    
                    // Add row click handlers
                    if (options && options.rowClick) {
                        $table.find('tbody tr').css('cursor', 'pointer').on('click', options.rowClick);
                    }
                    
                    // Add sorting indicators
                    $table.find('th[data-sort]').each(function() {
                        var $th = $(this);
                        $th.append(' <i class="bi bi-arrow-down-up sort-icon"></i>');
                    });
                    
                    return $table;
                },
                
                // Form validation with Bootstrap
                validateBootstrap: function(rules) {
                    var $form = $(this);
                    var isValid = true;
                    
                    // Clear previous validation states
                    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                    $form.find('.invalid-feedback, .valid-feedback').remove();
                    
                    // Validate each field
                    $.each(rules, function(fieldName, rule) {
                        var $field = $form.find('[name="' + fieldName + '"]');
                        var value = $field.val();
                        var fieldValid = true;
                        
                        // Required validation
                        if (rule.required && (!value || value.trim() === '')) {
                            fieldValid = false;
                        }
                        
                        // Length validation
                        if (rule.minLength && value.length < rule.minLength) {
                            fieldValid = false;
                        }
                        
                        if (rule.maxLength && value.length > rule.maxLength) {
                            fieldValid = false;
                        }
                        
                        // Pattern validation
                        if (rule.pattern && !new RegExp(rule.pattern).test(value)) {
                            fieldValid = false;
                        }
                        
                        // Email validation
                        if (rule.email && !KSP.isValidEmail(value)) {
                            fieldValid = false;
                        }
                        
                        // Phone validation
                        if (rule.phone && !KSP.isValidPhone(value)) {
                            fieldValid = false;
                        }
                        
                        // Update Bootstrap validation classes
                        if (fieldValid) {
                            $field.addClass('is-valid');
                            if (rule.successMessage) {
                                $field.after('<div class="valid-feedback">' + rule.successMessage + '</div>');
                            }
                        } else {
                            $field.addClass('is-invalid');
                            isValid = false;
                            if (rule.message) {
                                $field.after('<div class="invalid-feedback">' + rule.message + '</div>');
                            }
                        }
                    });
                    
                    return isValid;
                },
                
                // Smooth scroll to element
                scrollTo: function(duration) {
                    var target = $(this);
                    var duration = duration || 500;
                    
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, duration);
                    
                    return this;
                },
                
                // Fade in with Bootstrap
                fadeInBootstrap: function(duration) {
                    var $element = $(this);
                    var duration = duration || KSP.config.animationDuration;
                    
                    $element.css('opacity', 0).removeClass('d-none').animate({opacity: 1}, duration);
                    
                    return this;
                },
                
                // Fade out with Bootstrap
                fadeOutBootstrap: function(duration, callback) {
                    var $element = $(this);
                    var duration = duration || KSP.config.animationDuration;
                    
                    $element.animate({opacity: 0}, duration, function() {
                        $element.addClass('d-none');
                        if (callback) callback();
                    });
                    
                    return this;
                }
            });
        },
        
        // Bootstrap Setup and Extensions
        setupBootstrap: function() {
            // Initialize all Bootstrap components
            this.initializeTooltips();
            this.initializePopovers();
            this.initializeModals();
            this.initializeDropdowns();
            this.initializeTabs();
            this.initializeCollapse();
            
            // Bootstrap custom extensions
            this.extendBootstrapComponents();
        },
        
        initializeTooltips: function() {
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Auto-hide tooltips on mobile
            if (KSP.isMobile()) {
                $('[data-bs-toggle="tooltip"]').on('shown.bs.tooltip', function() {
                    var $this = $(this);
                    setTimeout(function() {
                        $this.tooltip('hide');
                    }, 2000);
                });
            }
        },
        
        initializePopovers: function() {
            // Initialize all popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        },
        
        initializeModals: function() {
            // Auto-focus first input in modals
            $('.modal').on('shown.bs.modal', function() {
                var $modal = $(this);
                var $firstInput = $modal.find('input:visible, textarea:visible').first();
                if ($firstInput.length) {
                    $firstInput.focus();
                }
            });
            
            // Handle form submission in modals
            $('.modal form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $modal = $form.closest('.modal');
                
                // Submit via AJAX
                KSP.submitFormAjax($form, function(response) {
                    if (response.success) {
                        $modal.modal('hide');
                        KSP.showNotification(response.message || 'Success', 'success');
                    } else {
                        KSP.showNotification(response.message || 'Error', 'danger');
                    }
                });
            });
        },
        
        initializeDropdowns: function() {
            // Close dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });
            
            // Keyboard navigation for dropdowns
            $(document).on('keydown', '.dropdown', function(e) {
                var $dropdown = $(this);
                var $menu = $dropdown.find('.dropdown-menu');
                
                if (e.keyCode === 27) { // Escape
                    $menu.removeClass('show');
                } else if (e.keyCode === 40) { // Down arrow
                    e.preventDefault();
                    var $items = $menu.find('.dropdown-item:not(.disabled)');
                    var $active = $items.filter('.active');
                    
                    if ($active.length === 0) {
                        $items.first().addClass('active');
                    } else {
                        $active.removeClass('active').next().addClass('active');
                    }
                } else if (e.keyCode === 38) { // Up arrow
                    e.preventDefault();
                    var $items = $menu.find('.dropdown-item:not(.disabled)');
                    var $active = $items.filter('.active');
                    
                    if ($active.length === 0) {
                        $items.last().addClass('active');
                    } else {
                        $active.removeClass('active').prev().addClass('active');
                    }
                }
            });
        },
        
        initializeTabs: function() {
            // Auto-save tab state
            $('.nav-tabs [data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                localStorage.setItem('activeTab', target);
            });
            
            // Restore tab state on page load
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-tabs [href="' + activeTab + '"]').tab('show');
            }
        },
        
        initializeCollapse: function() {
            // Auto-save collapse state
            $('.collapse').on('shown.bs.collapse', function() {
                var id = $(this).attr('id');
                localStorage.setItem('collapse_' + id, 'shown');
            });
            
            $('.collapse').on('hidden.bs.collapse', function() {
                var id = $(this).attr('id');
                localStorage.setItem('collapse_' + id, 'hidden');
            });
            
            // Restore collapse state on page load
            $('.collapse').each(function() {
                var id = $(this).attr('id');
                var state = localStorage.getItem('collapse_' + id);
                if (state === 'shown') {
                    $(this).collapse('show');
                }
            });
        },
        
        extendBootstrapComponents: function() {
            // Enhanced Bootstrap alerts
            $.fn.alertEnhanced = function(options) {
                var $alert = $(this);
                
                // Auto-dismiss after timeout
                if (options && options.autoDismiss) {
                    setTimeout(function() {
                        $alert.alert('close');
                    }, options.autoDismiss);
                }
                
                // Add close button if not present
                if (!$alert.find('.btn-close').length) {
                    $alert.append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
                }
                
                return $alert;
            };
            
            // Enhanced Bootstrap buttons
            $.fn.buttonEnhanced = function(options) {
                var $button = $(this);
                
                // Loading state
                if (options && options.loading) {
                    $button.prop('disabled', true);
                    $button.data('original-text', $button.html());
                    $button.html('<span class="spinner-border spinner-border-sm me-2"></span>' + (options.loadingText || KSP.config.loadingText));
                }
                
                // Reset state
                if (options && options.reset) {
                    $button.prop('disabled', false);
                    $button.html($button.data('original-text') || $button.html());
                }
                
                return $button;
            };
        },
        
        // jQuery + Bootstrap Integration
        setupIntegrations: function() {
            // Form validation integration
            this.setupFormValidation();
            
            // Table integration
            this.setupTableIntegration();
            
            // Modal integration
            this.setupModalIntegration();
            
            // Notification integration
            this.setupNotificationIntegration();
        },
        
        setupFormValidation: function() {
            // jQuery validation with Bootstrap styling
            $('form[data-validate]').each(function() {
                var $form = $(this);
                var rules = $form.data('validate-rules') || {};
                
                $form.on('submit', function(e) {
                    e.preventDefault();
                    
                    if ($form.validateBootstrap(rules)) {
                        KSP.submitFormAjax($form, function(response) {
                            if (response.success) {
                                KSP.showNotification(response.message || 'Success', 'success');
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            } else {
                                KSP.showNotification(response.message || 'Error', 'danger');
                            }
                        });
                    }
                });
            });
        },
        
        setupTableIntegration: function() {
            // Enhanced tables with jQuery + Bootstrap
            $('.table[data-enhanced]').each(function() {
                var $table = $(this);
                var options = $table.data('enhanced-options') || {};
                
                $table.tableEnhanced(options);
                
                // Add row selection
                if (options.selectable) {
                    $table.find('tbody tr').on('click', function() {
                        $(this).toggleClass('table-active');
                    });
                }
                
                // Add bulk actions
                if (options.bulkActions) {
                    KSP.setupBulkActions($table);
                }
            });
        },
        
        setupModalIntegration: function() {
            // Enhanced modals with jQuery + Bootstrap
            $('.modal[data-enhanced]').each(function() {
                var $modal = $(this);
                var options = $modal.data('enhanced-options') || {};
                
                $modal.modalEnhanced(options);
                
                // Handle AJAX content loading
                if (options.ajaxContent) {
                    $modal.on('show.bs.modal', function() {
                        var url = options.ajaxContent;
                        $.get(url, function(data) {
                            $modal.find('.modal-body').html(data);
                        });
                    });
                }
            });
        },
        
        setupNotificationIntegration: function() {
            // jQuery + Bootstrap notifications
            window.showNotification = function(message, type, options) {
                KSP.showNotification(message, type, options);
            };
            
            window.hideNotification = function(notification) {
                KSP.hideNotification(notification);
            };
        },
        
        // Mobile Optimizations
        setupMobileOptimizations: function() {
            if (KSP.isMobile()) {
                this.optimizeForMobile();
            }
        },
        
        optimizeForMobile: function() {
            // Touch-friendly interfaces
            $('.btn, .nav-link, .form-control').addClass('touch-target');
            
            // Mobile sidebar
            this.setupMobileSidebar();
            
            // Mobile tables
            $('.table').each(function() {
                if (!$(this).parent().hasClass('table-responsive')) {
                    $(this).wrap('<div class="table-responsive"></div>');
                }
            });
            
            // Mobile modals
            $('.modal').addClass('modal-mobile');
            
            // Mobile forms
            $('.form-group').addClass('mb-3');
        },
        
        setupMobileSidebar: function() {
            // Mobile sidebar toggle
            $('.mobile-menu-toggle').on('click', function() {
                $('.mobile-sidebar').toggleClass('show');
                $('.mobile-sidebar-overlay').toggleClass('show');
            });
            
            $('.mobile-sidebar-overlay').on('click', function() {
                $('.mobile-sidebar').removeClass('show');
                $(this).removeClass('show');
            });
        },
        
        // Event Handlers
        setupEventHandlers: function() {
            // Global error handler
            $(document).ajaxError(function(event, xhr, settings, error) {
                console.error('Kesalahan AJAX:', error);
                if (xhr.status === 401) {
                    window.location.href = '/ksp_peb/login.php';
                }
            });
            
            // Form submission handlers
            $(document).on('submit', 'form[data-ajax]', function(e) {
                e.preventDefault();
                var $form = $(this);
                KSP.submitFormAjax($form);
            });
            
            // Confirmation handlers
            $(document).on('click', '[data-confirm]', function(e) {
                var message = $(this).data('confirm');
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
            
            // Loading handlers
            $(document).on('click', '[data-loading]', function(e) {
                var $button = $(this);
                var loadingText = $button.data('loading') || KSP.config.loadingText;
                
                $button.buttonEnhanced({
                    loading: true,
                    loadingText: loadingText
                });
            });
            
            // Auto-save handlers
            $(document).on('input', '[data-auto-save]', function() {
                var $input = $(this);
                var url = $input.data('auto-save');
                var data = {};
                data[$input.attr('name')] = $input.val();
                
                KSP.debounce(function() {
                    $.post(url, data);
                }, KSP.config.debounceDelay)();
            });
        },
        
        // AJAX Setup
        setupAjaxDefaults: function() {
            // CSRF token setup
            var token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
            }
        },
        
        // Utility Functions
        isMobile: function() {
            return window.innerWidth <= 768;
        },
        
        isTablet: function() {
            return window.innerWidth > 768 && window.innerWidth <= 1024;
        },
        
        isDesktop: function() {
            return window.innerWidth > 1024;
        },
        
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var context = this;
                var args = arguments;
                var later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        },
        
        formatCurrency: function(e) {
            var value = e.target.value.replace(/[^\d]/g, '');
            var formatted = new Intl.NumberFormat('id-ID').format(value);
            e.target.value = formatted;
        },
        
        formatPhone: function(e) {
            var value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 0) {
                if (value.length <= 4) {
                    e.target.value = value;
                } else if (value.length <= 8) {
                    e.target.value = value.slice(0, 4) + '-' + value.slice(4);
                } else {
                    e.target.value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
                }
            }
        },
        
        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },
        
        isValidPhone: function(phone) {
            var regex = /^[\d\s\-\+\(\)]+$/;
            return regex.test(phone) && phone.replace(/\D/g, '').length >= 10;
        },
        
        submitFormAjax: function($form, callback) {
            var url = $form.attr('action') || window.location.href;
            var method = $form.attr('method') || 'POST';
            var data = $form.serialize();
            
            $.ajax({
                url: url,
                method: method,
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (callback) {
                        callback(response);
                    }
                },
                error: function(xhr, status, error) {
                    KSP.handleAjaxError(xhr, status, error);
                }
            });
        },
        
        handleAjaxError: function(xhr, status, error) {
            console.error('Kesalahan AJAX:', error);
            
            if (xhr.status === 401) {
                window.location.href = '/ksp_peb/login.php';
            } else if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(field, messages) {
                    var $input = $('[name="' + field + '"]');
                    $input.addClass('is-invalid');
                    $input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                });
            } else {
                KSP.showNotification(KSP.config.errorText, 'danger');
            }
        },
        
        showLoading: function() {
            $('#loadingOverlay').fadeInBootstrap();
        },
        
        hideLoading: function() {
            $('#loadingOverlay').fadeOutBootstrap();
        },
        
        showNotification: function(message, type, options) {
            var id = 'notification-' + Date.now();
            var className = 'alert-' + (type || 'info');
            var autoDismiss = (options && options.autoDismiss !== undefined) ? options.autoDismiss : 5000;
            
            var notification = `
                <div id="${id}" class="alert ${className} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('body').append(notification);
            
            var $notification = $('#' + id);
            $notification.alertEnhanced({autoDismiss: autoDismiss});
            
            // Auto-remove after dismiss
            $notification.on('closed.bs.alert', function() {
                $(this).remove();
            });
        },
        
        hideNotification: function(notification) {
            if (typeof notification === 'string') {
                $('#' + notification).alert('close');
            } else {
                $(notification).alert('close');
            }
        },
        
        setupBulkActions: function($table) {
            // Add checkbox column
            $table.find('thead tr').prepend('<th><input type="checkbox" class="form-check-input bulk-select-all"></th>');
            $table.find('tbody tr').prepend('<td><input type="checkbox" class="form-check-input bulk-select"></td>');
            
            // Handle select all
            $('.bulk-select-all').on('change', function() {
                $('.bulk-select').prop('checked', $(this).prop('checked'));
            });
            
            // Handle individual selection
            $('.bulk-select').on('change', function() {
                var allChecked = $('.bulk-select').length === $('.bulk-select:checked').length;
                $('.bulk-select-all').prop('checked', allChecked);
            });
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        KSP.init();
    });

})(jQuery);
