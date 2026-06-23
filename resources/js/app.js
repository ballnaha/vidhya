var revealObserver;
var scrollTopButton;
var mobileMenuShell;
var toastRoot;
var toastEventsReady = false;
var toastLimit = 5;
var contactServiceOutsideReady = false;
var recaptchaScriptPromise;
var adminShellReady = false;
var Sortable;
var sortableModulePromise;

function initSortableAdminPages() {
    if (!document.querySelector('[data-admin-directors], [data-admin-faqs], [data-admin-services], [data-admin-portfolios]')) {
        return;
    }

    function initialize() {
        initAdminDirectors();
        initAdminFaqs();
        initAdminServices();
        initAdminPortfolios();
    }

    if (Sortable) {
        initialize();
        return;
    }

    sortableModulePromise ??= import('sortablejs').then(function (module) {
        Sortable = module.default;
    });

    sortableModulePromise.then(initialize).catch(function (error) {
        console.error('Unable to load drag and drop controls.', error);
    });
}

function initHomeAnimations() {
    document.documentElement.classList.add('home-animations-ready');

    if (revealObserver) {
        revealObserver.disconnect();
    }

    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('[data-reveal]').forEach(function (element) {
            element.classList.add('is-visible');
        });
        return;
    }

    revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-reveal]').forEach(function (element) {
        revealObserver.observe(element);
    });
}

function syncScrollTopButton() {
    if (!scrollTopButton) {
        return;
    }

    scrollTopButton.toggleAttribute('data-visible', window.scrollY > 420);
}

function syncMarketingHeader() {
    var header = document.querySelector('[data-mobile-menu-shell]');

    if (header) {
        header.toggleAttribute('data-scrolled', window.scrollY > 0);
    }
}

function initMarketingHeader() {
    syncMarketingHeader();
}

function initAdminHomeForm() {
    document.querySelectorAll('[data-admin-home-form]:not([data-admin-home-ready])').forEach(function (form) {
        var save = form.querySelector('[data-admin-home-save]');
        var spinner = form.querySelector('[data-admin-home-spinner]');
        var label = form.querySelector('[data-admin-home-save-label]');

        form.setAttribute('data-admin-home-ready', '');

        form.addEventListener('submit', function () {
            if (save) {
                save.disabled = true;
            }

            if (spinner) {
                spinner.classList.remove('hidden');
            }

            if (label) {
                label.textContent = 'Saving...';
            }
        });
    });
}

function initScrollTopButton() {
    if (scrollTopButton && document.body.contains(scrollTopButton)) {
        syncScrollTopButton();
        return;
    }

    scrollTopButton = document.createElement('button');
    scrollTopButton.type = 'button';
    scrollTopButton.className = 'vidhya-scroll-top';
    scrollTopButton.setAttribute('aria-label', 'Scroll to top');
    scrollTopButton.textContent = '↑';
    scrollTopButton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            left: 0,
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
        });
    });

    document.body.appendChild(scrollTopButton);
    syncScrollTopButton();
}

function setMobileMenuOpen(open) {
    if (!mobileMenuShell) {
        return;
    }

    var toggle = mobileMenuShell.querySelector('[data-mobile-menu-toggle]');

    mobileMenuShell.toggleAttribute('data-mobile-menu-open', open);

    if (toggle) {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
}

function initMobileMenu() {
    mobileMenuShell = document.querySelector('[data-mobile-menu-shell]');

    if (!mobileMenuShell || mobileMenuShell.hasAttribute('data-mobile-menu-ready')) {
        return;
    }

    mobileMenuShell.setAttribute('data-mobile-menu-ready', '');

    mobileMenuShell.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-mobile-menu-toggle]');

        if (toggle) {
            setMobileMenuOpen(!mobileMenuShell.hasAttribute('data-mobile-menu-open'));
            return;
        }

        if (event.target.closest('[data-mobile-menu-close]')) {
            setMobileMenuOpen(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setMobileMenuOpen(false);
        }
    });
}

function getToastRoot() {
    if (toastRoot) {
        return toastRoot;
    }

    toastRoot = document.createElement('div');
    toastRoot.className = 'vidhya-toast-root';
    toastRoot.dataset.position = 'bottom';
    toastRoot.setAttribute('aria-live', 'polite');
    toastRoot.setAttribute('aria-atomic', 'false');
    document.body.appendChild(toastRoot);

    return toastRoot;
}

function syncToastStack() {
    if (!toastRoot) {
        return;
    }

    var visibleToasts = Array.from(toastRoot.querySelectorAll('.vidhya-toast:not([data-leaving])'));

    visibleToasts.forEach(function (toast, index) {
        toast.style.setProperty('--toast-stack-index', index);
        toast.toggleAttribute('data-stack-newest', index === visibleToasts.length - 1);
    });

    toastRoot.toggleAttribute('data-has-stack', visibleToasts.length > 1);
}

function dismissToast(toast) {
    if (!toast || toast.hasAttribute('data-leaving')) {
        return;
    }

    toast.setAttribute('data-leaving', '');
    toast.removeAttribute('data-visible');
    syncToastStack();

    window.setTimeout(function () {
        toast.remove();
        syncToastStack();
    }, 300);
}

function showFluxToast(variant, heading, text) {
    var fluxToast = document.querySelector('ui-toast-group, ui-toast');
    var detail = {
        slots: {
            heading: heading || '',
            text: text || '',
        },
        dataset: {
            variant: variant || 'success',
        },
        duration: 4200,
    };

    if (!fluxToast || typeof fluxToast.showToast !== 'function') {
        return false;
    }

    fluxToast.showToast(detail);

    return true;
}

function showCustomToast(variant, heading, text) {
    var root = getToastRoot();
    var toast = document.createElement('div');
    var title = document.createElement('strong');
    var message = document.createElement('span');

    toast.className = 'vidhya-toast vidhya-toast--' + variant;
    toast.setAttribute('role', 'status');
    title.textContent = heading;
    message.textContent = text;
    toast.append(title, message);

    root.appendChild(toast);
    syncToastStack();

    var activeToasts = Array.from(root.querySelectorAll('.vidhya-toast:not([data-leaving])'));

    activeToasts
        .slice(0, Math.max(0, activeToasts.length - toastLimit))
        .forEach(dismissToast);

    requestAnimationFrame(function () {
        toast.setAttribute('data-visible', '');
        syncToastStack();
    });

    window.setTimeout(function () {
        dismissToast(toast);
    }, 4200);
}

function showToast(variant, heading, text) {
    if (showFluxToast(variant, heading, text)) {
        return;
    }

    showCustomToast(variant, heading, text);
}

function initToastEvents() {
    document.querySelectorAll('[data-toast-on-load]:not([data-toast-shown])').forEach(function (trigger) {
        trigger.setAttribute('data-toast-shown', '');

        showToast(
            trigger.dataset.toastVariant || 'success',
            trigger.dataset.toastHeading || '',
            trigger.dataset.toastText || ''
        );
    });

    if (toastEventsReady) {
        return;
    }

    toastEventsReady = true;

    document.addEventListener('vidhya-toast', function (event) {
        var detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;

        if (!detail) {
            return;
        }

        showToast(detail.variant || 'success', detail.heading || '', detail.text || '');
    });
}

function setContactFieldError(field, hasError) {
    if (!field) {
        return;
    }

    field.toggleAttribute('data-contact-error', hasError);
}

function validateContactForm(form) {
    var name = form.elements.name;
    var email = form.elements.email;
    var message = form.elements.message;
    var emailValue = email.value.trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    [name, email, message].forEach(function (field) {
        setContactFieldError(field, false);
    });

    if (!name.value.trim()) {
        setContactFieldError(name, true);
        return { valid: false, field: name, message: 'Please enter your full name.' };
    }

    if (!emailValue) {
        setContactFieldError(email, true);
        return { valid: false, field: email, message: 'Please enter your email address.' };
    }

    if (!emailPattern.test(emailValue)) {
        setContactFieldError(email, true);
        return { valid: false, field: email, message: 'Please enter a valid email address.' };
    }

    if (!message.value.trim()) {
        setContactFieldError(message, true);
        return { valid: false, field: message, message: 'Please tell us about your project.' };
    }

    return { valid: true };
}

function closeContactService(service) {
    if (!service) {
        return;
    }

    var toggle = service.querySelector('[data-contact-service-toggle]');

    service.removeAttribute('data-open');

    if (toggle) {
        toggle.setAttribute('aria-expanded', 'false');
    }
}

function getContactRecaptchaToken(siteKey) {
    if (!siteKey) {
        return Promise.reject(new Error('reCAPTCHA is not configured.'));
    }

    if (!recaptchaScriptPromise) {
        recaptchaScriptPromise = new Promise(function (resolve, reject) {
            if (window.grecaptcha) {
                resolve(window.grecaptcha);
                return;
            }

            var script = document.createElement('script');
            script.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(siteKey);
            script.async = true;
            script.defer = true;
            script.onload = function () { resolve(window.grecaptcha); };
            script.onerror = function () { reject(new Error('Unable to load reCAPTCHA.')); };
            document.head.appendChild(script);
        });
    }

    return recaptchaScriptPromise.then(function (grecaptcha) {
        return new Promise(function (resolve, reject) {
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'contact' }).then(resolve).catch(reject);
            });
        });
    });
}

function initContactForm() {
    document.querySelectorAll('[data-contact-shell]').forEach(function (shell) {
        if (shell.hasAttribute('data-contact-ready')) {
            return;
        }

        var form = shell.querySelector('[data-contact-form]');
        var success = shell.querySelector('[data-contact-success]');
        var submit = shell.querySelector('[data-contact-submit]');
        var submitLabel = shell.querySelector('[data-contact-submit-label]');
        var service = shell.querySelector('[data-contact-service]');
        var serviceInput = shell.querySelector('[data-contact-service-input]');
        var serviceLabel = shell.querySelector('[data-contact-service-label]');
        var serviceToggle = shell.querySelector('[data-contact-service-toggle]');

        if (!form) {
            return;
        }

        shell.setAttribute('data-contact-ready', '');

        if (service && serviceToggle) {
            serviceToggle.addEventListener('click', function () {
                var open = !service.hasAttribute('data-open');

                service.toggleAttribute('data-open', open);
                serviceToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            service.addEventListener('click', function (event) {
                var option = event.target.closest('[data-contact-service-option]');

                if (!option) {
                    return;
                }

                var value = option.getAttribute('value') || '';

                serviceInput.value = value;
                serviceLabel.textContent = value || 'Service Needed';
                serviceLabel.classList.toggle('text-white/28', !value);

                service.querySelectorAll('[data-contact-service-option]').forEach(function (item) {
                    var selected = item === option;

                    item.toggleAttribute('data-selected', selected);
                    item.setAttribute('aria-selected', selected ? 'true' : 'false');
                });

                closeContactService(service);
            });
        }

        form.addEventListener('input', function (event) {
            if (event.target.matches('[data-contact-field]')) {
                setContactFieldError(event.target, false);
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var result = validateContactForm(form);

            if (!result.valid) {
                showToast('danger', 'Please check the form.', result.message);
                result.field.focus();
                return;
            }

            submit.disabled = true;
            submitLabel.textContent = 'Verifying...';

            getContactRecaptchaToken(shell.dataset.recaptchaSiteKey).then(function (token) {
                var formData = new FormData(form);
                formData.set('recaptcha_token', token);
                submitLabel.textContent = 'Sending...';

                return fetch(shell.dataset.contactSubmitUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: formData,
                });
            }).then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (payload) {
                    if (!response.ok) {
                        var firstError = Object.values(payload.errors || {})[0];
                        throw new Error(Array.isArray(firstError) ? firstError[0] : (payload.message || 'Unable to send your message.'));
                    }

                    return payload;
                });
            }).then(function (payload) {
                form.reset();

                if (serviceLabel) {
                    serviceLabel.textContent = 'Service Needed';
                    serviceLabel.classList.add('text-white/28');
                }

                if (serviceInput) {
                    serviceInput.value = '';
                }

                if (service) {
                    service.querySelectorAll('[data-contact-service-option]').forEach(function (item) {
                        var selected = !item.getAttribute('value');

                        item.toggleAttribute('data-selected', selected);
                        item.setAttribute('aria-selected', selected ? 'true' : 'false');
                    });
                }

                if (success) {
                    form.classList.add('hidden');
                    success.classList.remove('hidden');
                    success.classList.add('flex');
                }

                showToast('success', 'Message received.', payload.message || "Thank you for reaching out. We'll be in touch within 24 hours.");
            }).catch(function (error) {
                showToast('danger', 'Unable to send message.', error.message || 'Please try again or contact us directly by email.');
            }).finally(function () {
                submit.disabled = false;
                submitLabel.textContent = 'Send Message';
            });
        });
    });

    if (!contactServiceOutsideReady) {
        contactServiceOutsideReady = true;

        document.addEventListener('click', function (event) {
            document.querySelectorAll('[data-contact-service][data-open]').forEach(function (service) {
                if (!service.contains(event.target)) {
                    closeContactService(service);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('[data-contact-service][data-open]').forEach(function (service) {
                closeContactService(service);
            });
        });
    }
}

function initDirectorTabs() {
    document.querySelectorAll('[data-director-tabs]:not([data-director-tabs-ready])').forEach(function (shell) {
        var tabs = Array.from(shell.querySelectorAll('[data-director-tab]'));
        var panels = Array.from(shell.querySelectorAll('[data-director-panel]'));
        var activeModal = null;

        var videoModal = document.querySelector('[data-director-video-modal]');
        var videoPlayer = videoModal?.querySelector('[data-director-video-player]');
        var videoIframe = videoModal?.querySelector('[data-director-video-iframe]');
        var videoImage = videoModal?.querySelector('[data-director-video-image]');
        var videoTitle = videoModal?.querySelector('[data-director-video-title]');

        if (!tabs.length || !panels.length) {
            return;
        }

        function closeModal() {
            if (!activeModal) {
                return;
            }

            activeModal.classList.add('hidden');
            activeModal = null;
            document.body.classList.remove('overflow-hidden');
        }

        function openModal(id) {
            closeModal();

            activeModal = shell.querySelector('[data-director-work-modal="' + id + '"]');

            if (!activeModal) {
                return;
            }

            activeModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            activeModal.querySelector('[data-director-work-close]')?.focus();
        }

        function playVideo(url, title, isImage) {
            if (!videoModal) return;

            if (videoTitle) {
                videoTitle.textContent = title || '';
            }

            if (isImage) {
                if (videoPlayer) {
                    videoPlayer.src = '';
                    videoPlayer.classList.add('hidden');
                }
                if (videoIframe) {
                    videoIframe.src = '';
                    videoIframe.classList.add('hidden');
                }
                if (videoImage) {
                    videoImage.src = url;
                    videoImage.classList.remove('hidden');
                }
            } else {
                if (videoImage) {
                    videoImage.src = '';
                    videoImage.classList.add('hidden');
                }

                var isYoutube = url.indexOf('youtube.com') > -1 || url.indexOf('youtu.be') > -1;
                var isVimeo = url.indexOf('vimeo.com') > -1;

                if (isYoutube || isVimeo) {
                    var embedUrl = url;
                    if (isYoutube) {
                        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                        var match = url.match(regExp);
                        if (match && match[2].length === 11) {
                            embedUrl = 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1';
                        }
                    } else if (isVimeo) {
                        var regExp = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
                        var match = url.match(regExp);
                        if (match) {
                            embedUrl = 'https://player.vimeo.com/video/' + match[1] + '?autoplay=1';
                        }
                    }

                    if (videoPlayer) {
                        videoPlayer.src = '';
                        videoPlayer.classList.add('hidden');
                    }
                    if (videoIframe) {
                        videoIframe.src = embedUrl;
                        videoIframe.classList.remove('hidden');
                    }
                } else {
                    if (videoIframe) {
                        videoIframe.src = '';
                        videoIframe.classList.add('hidden');
                    }
                    if (videoPlayer) {
                        videoPlayer.src = url;
                        videoPlayer.classList.remove('hidden');
                        videoPlayer.play().catch(function () {});
                    }
                }
            }

            videoModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeVideo() {
            if (!videoModal) return;

            videoModal.classList.add('hidden');

            if (videoPlayer) {
                videoPlayer.pause();
                videoPlayer.src = '';
            }
            if (videoIframe) {
                videoIframe.src = '';
            }
            if (videoImage) {
                videoImage.src = '';
            }

            if (!activeModal) {
                document.body.classList.remove('overflow-hidden');
            }
        }

        function activate(id, shouldScroll) {
            closeModal();
            closeVideo();

            tabs.forEach(function (tab) {
                var active = tab.dataset.directorTab === id;

                tab.toggleAttribute('data-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');

                if (active) {
                    document.title = tab.textContent.trim() + " - AI Director - Vidhya Studio";
                }
            });

            panels.forEach(function (panel) {
                panel.classList.toggle('hidden', panel.dataset.directorPanel !== id);
            });

            if (shouldScroll) {
                var selector = shell.querySelector('[data-director-tabs-selector]');

                window.scrollTo({
                    top: Math.max(0, (selector || shell).getBoundingClientRect().top + window.scrollY - 72),
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                });
            }

            requestAnimationFrame(initHomeAnimations);
        }

        shell.setAttribute('data-director-tabs-ready', '');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                activate(tab.dataset.directorTab, true);
            });
        });

        shell.addEventListener('click', function (event) {
            var opener = event.target.closest('[data-director-work-open]');
            var closer = event.target.closest('[data-director-work-close]');
            var modal = event.target.closest('[data-director-work-modal]');
            var videoTrigger = event.target.closest('[data-video-url]');
            var imageTrigger = event.target.closest('[data-image-url]');

            if (videoTrigger) {
                var url = videoTrigger.getAttribute('data-video-url');
                var title = videoTrigger.getAttribute('data-video-title');
                playVideo(url, title, false);
                return;
            }

            if (imageTrigger) {
                var url = imageTrigger.getAttribute('data-image-url');
                var title = imageTrigger.getAttribute('data-video-title');
                playVideo(url, title, true);
                return;
            }

            if (opener) {
                openModal(opener.dataset.directorWorkOpen);
                return;
            }

            if (closer || (modal && event.target === modal)) {
                closeModal();
            }
        });

        if (videoModal) {
            var videoClose = videoModal.querySelector('[data-director-video-close]');
            videoClose?.addEventListener('click', closeVideo);
            videoModal.addEventListener('click', function (event) {
                if (event.target === videoModal) {
                    closeVideo();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeVideo();
                closeModal();
            }
        });

        activate(tabs[0].dataset.directorTab, false);
    });
}

function initAdminUsers() {
    document.querySelectorAll('[data-admin-users]:not([data-admin-users-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-users-initial]');
        var table = shell.querySelector('[data-admin-users-table]');
        var formShell = shell.querySelector('[data-admin-users-form-shell]');
        var form = shell.querySelector('[data-admin-users-form]');
        var search = shell.querySelector('[data-admin-users-search]');
        var deleteModal = shell.querySelector('[data-admin-users-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-users-delete-confirm]');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var users = [];
        var deletingId = null;
        var searchTimer;

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-users-ready', '');

        try {
            users = JSON.parse(initialData?.textContent || '[]');
        } catch (error) {
            users = [];
        }

        function userUrl(template, userId) {
            return template.replace('__USER__', userId);
        }

        function request(url, options) {
            return fetch(url, Object.assign({
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            }, options || {})).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }

                    return payload;
                });
            });
        }

        function toast(variant, heading, text) {
            showToast(variant, heading, text);
        }

        function setBusy(busy) {
            var save = shell.querySelector('[data-admin-users-save]');
            var spinner = shell.querySelector('[data-admin-users-spinner]');
            var label = shell.querySelector('[data-admin-users-save-label]');

            if (save) {
                save.disabled = busy;
            }

            spinner?.classList.toggle('hidden', !busy);

            if (label) {
                label.textContent = busy ? 'Saving...' : 'Save';
            }
        }

        function clearErrors() {
            shell.querySelectorAll('[data-admin-users-error]').forEach(function (error) {
                error.textContent = '';
                error.classList.add('hidden');
            });

            shell.querySelectorAll('[data-admin-users-field]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });
        }

        function showErrors(errors) {
            Object.entries(errors || {}).forEach(function ([name, messages]) {
                var error = shell.querySelector('[data-admin-users-error="' + name + '"]');
                var field = shell.querySelector('[data-admin-users-field="' + name + '"]');

                if (error) {
                    error.textContent = Array.isArray(messages) ? messages[0] : messages;
                    error.classList.remove('hidden');
                }

                if (field) {
                    field.classList.remove('border-white/10');
                    field.classList.add('border-red-500', 'focus:border-red-500');
                }
            });
        }

        function validateAdminUserPayload(payload, isEditing) {
            var errors = {};
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!payload.name.trim()) {
                errors.name = ['Please enter a name.'];
            }

            if (!payload.email.trim()) {
                errors.email = ['Please enter an email address.'];
            } else if (!emailPattern.test(payload.email.trim())) {
                errors.email = ['Please enter a valid email address.'];
            }

            if (!isEditing && !payload.password.trim()) {
                errors.password = ['Please enter a password.'];
            } else if (payload.password.trim() && payload.password.length < 8) {
                errors.password = ['The password must be at least 8 characters.'];
            }

            if (!['admin', 'user'].includes(payload.role)) {
                errors.role = ['Please select a valid role.'];
            }

            return errors;
        }

        function resetForm() {
            form.reset();
            clearErrors();
            form.querySelector('[data-admin-users-id]').value = '';
            shell.querySelector('[data-admin-users-form-title]').textContent = 'Create User';
            form.querySelector('[name="password"]').placeholder = 'Password';
            form.elements.role.value = 'user';
            formShell.classList.add('hidden');
        }

        function openForm(user) {
            clearErrors();
            formShell.classList.remove('hidden');
            form.querySelector('[data-admin-users-id]').value = user?.id || '';
            form.elements.name.value = user?.name || '';
            form.elements.email.value = user?.email || '';
            form.elements.password.value = '';
            form.elements.role.value = user?.role || 'user';
            shell.querySelector('[data-admin-users-form-title]').textContent = user ? 'Edit User' : 'Create User';
            form.querySelector('[name="password"]').placeholder = user ? 'New password optional' : 'Password';
            form.elements.name.focus();
        }

        function filteredUsers() {
            var value = (search?.value || '').trim().toLowerCase();

            if (!value) {
                return users;
            }

            return users.filter(function (user) {
                return user.name.toLowerCase().includes(value) || user.email.toLowerCase().includes(value);
            });
        }

        function render() {
            var rows = filteredUsers();

            table.replaceChildren();

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');

                emptyCell.colSpan = 5;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No users found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (user) {
                var row = document.createElement('tr');
                var nameCell = document.createElement('td');
                var nameWrap = document.createElement('div');
                var initials = document.createElement('div');
                var name = document.createElement('span');
                var email = document.createElement('td');
                var role = document.createElement('td');
                var roleBadge = document.createElement('span');
                var created = document.createElement('td');
                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.035]';
                nameCell.className = 'px-5 py-4';
                nameWrap.className = 'flex items-center gap-3';
                initials.className = 'flex size-9 items-center justify-center rounded border border-white/10 bg-white/[0.04] text-xs font-semibold uppercase text-white/75';
                initials.textContent = user.initials || '';
                name.className = 'font-medium text-white';
                name.textContent = user.name;
                email.className = 'px-5 py-4 text-white/48';
                email.textContent = user.email;
                role.className = 'px-5 py-4';
                roleBadge.className = 'rounded border border-white/10 bg-white/[0.04] px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-white/55';
                roleBadge.textContent = user.role || 'user';
                role.appendChild(roleBadge);
                created.className = 'px-5 py-4 text-white/35';
                created.textContent = user.created_at || '';
                actions.className = 'px-5 py-4';
                actionsWrap.className = 'flex justify-end gap-2';
                edit.type = 'button';
                edit.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                edit.textContent = 'Edit';
                edit.addEventListener('click', function () {
                    openForm(user);
                });
                remove.type = 'button';
                remove.className = 'rounded border border-[#e60012]/25 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-[#e60012]/55 hover:bg-[#e60012]/12 hover:text-white';
                remove.textContent = 'Delete';
                remove.addEventListener('click', function () {
                    deletingId = user.id;
                    deleteModal?.classList.remove('hidden');
                    deleteModal?.classList.add('grid');
                });

                nameWrap.append(initials, name);
                nameCell.appendChild(nameWrap);
                actionsWrap.append(edit, remove);
                actions.appendChild(actionsWrap);
                row.append(nameCell, email, role, created, actions);
                table.appendChild(row);
            });
        }

        function loadUsers() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            if (search?.value.trim()) {
                url.searchParams.set('search', search.value.trim());
            }

            return request(url.toString(), { cache: 'no-store' }).then(function (payload) {
                users = payload.users || [];
                render();
            }).catch(function (error) {
                toast('danger', 'Unable to load users', error.message || 'Please refresh the page and try again.');
            });
        }

        function checkDuplicateEmail(email, userId) {
            var url = new URL(shell.dataset.checkEmailUrl, window.location.origin);

            url.searchParams.set('email', email);

            if (userId) {
                url.searchParams.set('ignore', userId);
            }

            return request(url.toString()).then(function (payload) {
                return Boolean(payload.exists);
            });
        }

        shell.querySelector('[data-admin-users-create]')?.addEventListener('click', function () {
            openForm(null);
        });

        shell.querySelector('[data-admin-users-cancel]')?.addEventListener('click', resetForm);

        search?.addEventListener('input', function () {
            render();
            clearTimeout(searchTimer);
            searchTimer = window.setTimeout(loadUsers, 300);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            var id = form.querySelector('[data-admin-users-id]').value;
            var payload = {
                name: form.elements.name.value.trim(),
                email: form.elements.email.value.trim(),
                password: form.elements.password.value,
                role: form.elements.role.value,
            };
            var validationErrors = validateAdminUserPayload(payload, Boolean(id));
            var url = id ? userUrl(shell.dataset.updateUrlTemplate, id) : shell.dataset.storeUrl;
            var method = id ? 'PATCH' : 'POST';

            if (Object.keys(validationErrors).length) {
                showErrors(validationErrors);
                toast('danger', 'Please check the form', 'Some user details need attention.');
                return;
            }

            setBusy(true);

            checkDuplicateEmail(payload.email, id).then(function (exists) {
                if (exists) {
                    showErrors({
                        email: ['This email is already in use.'],
                    });
                    toast('danger', 'Please check the form', 'This email is already in use.');
                    return null;
                }

                return request(url, {
                    method: method,
                    body: JSON.stringify(payload),
                });
            }).then(function (response) {
                if (!response) {
                    return null;
                }

                resetForm();
                toast('success', 'Success', response.message || 'User saved.');
                return loadUsers();
            }).catch(function (error) {
                showErrors(error.errors || {});
                toast('danger', 'Unable to save', error.message || 'Please check the form.');
            }).finally(function () {
                setBusy(false);
            });
        });

        form.elements.email.addEventListener('blur', function () {
            var id = form.querySelector('[data-admin-users-id]').value;
            var email = form.elements.email.value.trim();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !emailPattern.test(email)) {
                return;
            }

            checkDuplicateEmail(email, id).then(function (exists) {
                if (!exists) {
                    return;
                }

                showErrors({
                    email: ['This email is already in use.'],
                });
            }).catch(function () {});
        });

        shell.querySelector('[data-admin-users-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) {
                return;
            }

            deleteConfirm.disabled = true;

            request(userUrl(shell.dataset.deleteUrlTemplate, deletingId), {
                method: 'DELETE',
            }).then(function (response) {
                toast('success', 'Success', response.message || 'User deleted.');
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');
                return loadUsers();
            }).catch(function (error) {
                toast('danger', 'Unable to delete', error.message || 'Please try again.');
            }).finally(function () {
                deleteConfirm.disabled = false;
            });
        });

        render();
        loadUsers();
    });
}

function initAdminDirectors() {
    document.querySelectorAll('[data-admin-directors]:not([data-admin-directors-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-directors-initial]');
        var table = shell.querySelector('[data-admin-directors-table]');
        var formShell = shell.querySelector('[data-admin-directors-form-shell]');
        var form = shell.querySelector('[data-admin-directors-form]');
        var search = shell.querySelector('[data-admin-directors-search]');
        var deleteModal = shell.querySelector('[data-admin-directors-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-directors-delete-confirm]');
        var deleteWorkModal = shell.querySelector('[data-admin-directors-delete-work-modal]');
        var deletingWorkRow = null;
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var directors = [];
        var deletingId = null;
        var searchTimer;
        var isSlugManuallyEdited = false;
        var worksSortable = null;

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-directors-ready', '');

        var worksContainer = shell.querySelector('[data-admin-directors-works-container]');
        var addWorkButton = shell.querySelector('[data-admin-directors-add-work]');
        var worksTemplate = shell.querySelector('[data-admin-directors-work-row-template]');
        var worksRawInput = shell.querySelector('[data-admin-directors-works-raw-input]');

        function initDragAndDrop() {
            if (!worksContainer) return;
            if (worksSortable) {
                worksSortable.destroy();
            }

            worksSortable = Sortable.create(worksContainer, {
                handle: '[data-admin-directors-work-drag-handle]',
                draggable: '[data-admin-directors-work-row]',
                animation: 180,
                ghostClass: 'opacity-25',
                chosenClass: 'border-[#366bc3]/40',
                dragClass: 'shadow-2xl',
                delay: 120,
                delayOnTouchOnly: true,
                touchStartThreshold: 4,
                fallbackTolerance: 3,
                fallbackOnBody: true,
                onEnd: function (event) {
                    if (event.oldIndex !== event.newIndex) {
                        renumberWorks();
                    }
                },
            });
        }

        initDragAndDrop();

        function renumberWorks() {
            if (!worksContainer) return;
            worksContainer.querySelectorAll('[data-admin-directors-work-row]').forEach(function (row, index) {
                var numberIndicator = row.querySelector('[data-admin-directors-work-number]');
                if (numberIndicator) {
                    numberIndicator.textContent = '#' + (index + 1);
                }
            });
        }

        function clearWorkTitleErrors() {
            if (!worksContainer) return;
            worksContainer.querySelectorAll('[data-admin-directors-work-field="title"]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });
        }

        function showWorkTitleErrors() {
            if (!worksContainer) return;
            worksContainer.querySelectorAll('[data-admin-directors-work-row]').forEach(function (row) {
                var titleField = row.querySelector('[data-admin-directors-work-field="title"]');
                if (!titleField || titleField.value.trim()) {
                    return;
                }

                titleField.classList.remove('border-white/10');
                titleField.classList.add('border-red-500', 'focus:border-red-500');
            });
        }

        function addWorkRow(data) {
            if (!worksContainer || !worksTemplate) return;
            var clone = worksTemplate.content.cloneNode(true);
            var row = clone.querySelector('[data-admin-directors-work-row]');

            var titleField = row.querySelector('[data-admin-directors-work-field="title"]');
            var videoUrlField = row.querySelector('[data-admin-directors-work-field="video_url"]');
            var imageField = row.querySelector('[data-admin-directors-work-field="image"]');
            var spanField = row.querySelector('[data-admin-directors-work-field="span"]');
            
            var fileInput = row.querySelector('[data-admin-directors-work-file-input]');
            var previewWrapper = row.querySelector('[data-admin-directors-work-preview-wrapper]');
            var previewImg = row.querySelector('[data-admin-directors-work-preview]');
            var placeholder = row.querySelector('[data-admin-directors-work-upload-placeholder]');

            var removeImageBtn = row.querySelector('[data-admin-directors-work-remove-image-file]');

            if (data) {
                if (titleField) titleField.value = data.title || '';
                if (videoUrlField) videoUrlField.value = data.video_url || '';
                if (imageField) imageField.value = data.image || '';
                if (spanField) spanField.value = data.span || 'md:col-span-2';

                if (data.image) {
                    if (previewImg) previewImg.src = data.image;
                    if (placeholder) placeholder.classList.add('hidden');
                    if (previewWrapper) previewWrapper.classList.remove('hidden');

                    var isYoutubeThumbnail = data.image.indexOf('youtube.com') > -1 || data.image.indexOf('ytimg.com') > -1;
                    if (removeImageBtn) {
                        removeImageBtn.classList.toggle('hidden', isYoutubeThumbnail);
                    }
                }
            }

            if (titleField) {
                titleField.addEventListener('input', function () {
                    if (!this.value.trim()) {
                        return;
                    }

                    this.classList.remove('border-red-500', 'focus:border-red-500');
                    this.classList.add('border-white/10');
                });
            }

            if (videoUrlField) {
                videoUrlField.addEventListener('input', function () {
                    // Custom cover uploads can coexist with a video URL.
                });
            }

            var uploadCard = row.querySelector('[data-admin-directors-work-upload-card]');
            if (uploadCard && fileInput) {
                uploadCard.addEventListener('click', function () {
                    fileInput.click();
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    var file = this.files[0];
                    if (file) {
                        if (previewImg) previewImg.src = URL.createObjectURL(file);
                        if (placeholder) placeholder.classList.add('hidden');
                        if (previewWrapper) previewWrapper.classList.remove('hidden');
                        if (imageField) imageField.value = '';

                        if (removeImageBtn) {
                            removeImageBtn.classList.remove('hidden');
                        }
                    }
                });
            }

            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (fileInput) fileInput.value = '';
                    if (imageField) imageField.value = '';
                    if (previewWrapper) previewWrapper.classList.add('hidden');
                    if (previewImg) previewImg.src = '';
                    if (placeholder) placeholder.classList.remove('hidden');
                });
            }

            row.querySelectorAll('[data-admin-directors-remove-work]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (deleteWorkModal) {
                        deletingWorkRow = row;
                        deleteWorkModal.classList.remove('hidden');
                        deleteWorkModal.classList.add('grid');
                    } else {
                        row.remove();
                        renumberWorks();
                    }
                });
            });

            worksContainer.appendChild(row);
            renumberWorks();
        }

        function serializeWorks() {
            if (!worksContainer || !worksRawInput) return;
            var works = [];
            var activeIndex = 0;
            worksContainer.querySelectorAll('[data-admin-directors-work-row]').forEach(function (row) {
                var title = row.querySelector('[data-admin-directors-work-field="title"]')?.value.trim() || '';
                var videoUrl = row.querySelector('[data-admin-directors-work-field="video_url"]')?.value.trim() || '';
                var image = row.querySelector('[data-admin-directors-work-field="image"]')?.value.trim() || '';
                var span = row.querySelector('[data-admin-directors-work-field="span"]')?.value || 'md:col-span-2';
                var showInPortfolio = true;
                
                var fileInput = row.querySelector('[data-admin-directors-work-file-input]');

                if (title || videoUrl || image || (fileInput && fileInput.files.length)) {
                    if (fileInput) {
                        fileInput.setAttribute('name', 'work_image_file_' + activeIndex);
                    }
                    works.push({
                        title: title,
                        video_url: videoUrl,
                        image: image,
                        span: span,
                        show_in_portfolio: showInPortfolio
                    });
                    activeIndex++;
                } else {
                    if (fileInput) {
                        fileInput.removeAttribute('name');
                    }
                }
            });

            worksRawInput.value = JSON.stringify(works);
        }

        if (addWorkButton) {
            addWorkButton.addEventListener('click', function () {
                addWorkRow(null);
            });
        }

        try {
            directors = JSON.parse(initialData?.textContent || '[]');
        } catch (error) {
            directors = [];
        }

        function directorUrl(template, directorId) {
            return template.replace('__DIRECTOR__', directorId);
        }

        function request(url, options) {
            var headers = {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            };
            if (options && options.body && !(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
            return fetch(url, Object.assign({
                headers: headers
            }, options || {})).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }

                    return payload;
                });
            });
        }

        function toast(variant, heading, text) {
            showToast(variant, heading, text);
        }

        function setBusy(busy) {
            var save = shell.querySelector('[data-admin-directors-save]');
            var spinner = shell.querySelector('[data-admin-directors-spinner]');
            var label = shell.querySelector('[data-admin-directors-save-label]');

            if (save) {
                save.disabled = busy;
            }

            spinner?.classList.toggle('hidden', !busy);

            if (label) {
                label.textContent = busy ? 'Saving...' : 'Save Director';
            }
        }

        function clearErrors() {
            shell.querySelectorAll('[data-admin-directors-error]').forEach(function (error) {
                error.textContent = '';
                error.classList.add('hidden');
            });

            shell.querySelectorAll('[data-admin-directors-field]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });

            clearWorkTitleErrors();
        }

        function showErrors(errors) {
            Object.entries(errors || {}).forEach(function ([name, messages]) {
                var error = shell.querySelector('[data-admin-directors-error="' + name + '"]');
                var field = shell.querySelector('[data-admin-directors-field="' + name + '"]');

                if (error) {
                    error.textContent = Array.isArray(messages) ? messages[0] : messages;
                    error.classList.remove('hidden');
                }

                if (field) {
                    field.classList.remove('border-white/10');
                    field.classList.add('border-red-500', 'focus:border-red-500');
                }

                if (name === 'works_raw') {
                    showWorkTitleErrors();
                }
            });
        }

        function validateDirectorPayload(payload, hasFile) {
            var errors = {};

            if (!payload.first_name.trim()) errors.first_name = ['Please enter a first name.'];
            if (!payload.last_name.trim()) errors.last_name = ['Please enter a last name.'];
            if (!payload.slug.trim()) {
                errors.slug = ['Please enter a slug.'];
            } else if (!/^[a-z0-9-_]+$/.test(payload.slug.trim())) {
                errors.slug = ['The slug must only contain lowercase letters, numbers, dashes, and underscores (Thai characters are not supported).'];
            }

            var isGeneral = payload.slug.trim() === 'general';

            if (!isGeneral) {
                if (!payload.eyebrow.trim()) errors.eyebrow = ['Please enter eyebrow subtitle.'];
                if (!payload.role.trim()) errors.role = ['Please enter role tagline.'];
                if (!payload.bio_title_white.trim()) errors.bio_title_white = ['Please enter bio title white text.'];
                if (!payload.bio_title_gradient.trim()) errors.bio_title_gradient = ['Please enter bio title gradient text.'];
                if (!payload.bio_alt.trim()) errors.bio_alt = ['Please enter bio image alt text.'];
                if (!payload.bio_raw.trim()) errors.bio_raw = ['Please enter biography paragraphs.'];
                
                if (!payload.stat_1_value.trim()) errors.stat_1_value = ['Required.'];
                if (!payload.stat_1_label.trim()) errors.stat_1_label = ['Required.'];
                if (!payload.stat_2_value.trim()) errors.stat_2_value = ['Required.'];
                if (!payload.stat_2_label.trim()) errors.stat_2_label = ['Required.'];
                if (!payload.stat_3_value.trim()) errors.stat_3_value = ['Required.'];
                if (!payload.stat_3_label.trim()) errors.stat_3_label = ['Required.'];

                if (!payload.works_eyebrow.trim()) errors.works_eyebrow = ['Please enter works section eyebrow.'];
                if (!payload.works_title_white.trim()) errors.works_title_white = ['Please enter works section title (white).'];
                if (!payload.works_title_muted.trim()) errors.works_title_muted = ['Please enter works section title (muted).'];
            }

            if (!payload.works_raw.trim()) {
                errors.works_raw = ['Please enter works configuration.'];
            } else {
                try {
                    var parsed = JSON.parse(payload.works_raw);
                    if (!Array.isArray(parsed)) {
                        errors.works_raw = ['The works must be a JSON array.'];
                    } else {
                        var missingTitle = false;
                        parsed.forEach(function (work) {
                            if (!work.title || !work.title.trim()) {
                                missingTitle = true;
                            }
                        });
                        if (missingTitle) {
                            errors.works_raw = ['Each work must have a title.'];
                        }
                    }
                } catch (e) {
                    errors.works_raw = ['Invalid JSON format: ' + e.message];
                }
            }

            return errors;
        }


        function resetForm() {
            form.reset();
            clearErrors();
            isSlugManuallyEdited = false;
            form.querySelector('[data-admin-directors-id]').value = '';
            shell.querySelector('[data-admin-directors-form-title]').textContent = 'Create Director';
            if (worksContainer) {
                worksContainer.replaceChildren();
            }
            formShell.classList.add('hidden');
        }

        function openForm(director, isDuplicate) {
            clearErrors();
            isSlugManuallyEdited = Boolean(director && !isDuplicate);
            formShell.classList.remove('hidden');
            form.querySelector('[data-admin-directors-id]').value = isDuplicate ? '' : (director?.id || '');
            
            form.elements.first_name.value = director ? (director.first_name + (isDuplicate ? ' (Copy)' : '')) : '';
            form.elements.last_name.value = director?.last_name || '';
            form.elements.slug.value = director ? (director.slug + (isDuplicate ? '-copy' : '')) : '';
            form.elements.eyebrow.value = director?.eyebrow || '';
            form.elements.role.value = director?.role || '';
            form.elements.bio_title_white.value = director?.bio_title_white || '';
            form.elements.bio_title_gradient.value = director?.bio_title_gradient || '';
            form.elements.bio_image.value = director?.bio_image || '';
            form.elements.bio_alt.value = director?.bio_alt || '';
            form.elements.works_eyebrow.value = director?.works_eyebrow || '';
            form.elements.works_title_white.value = director?.works_title_white || '';
            form.elements.works_title_muted.value = director?.works_title_muted || '';
            form.elements.bio_raw.value = director?.bio_raw || '';
            form.elements.works_raw.value = director?.works_raw || '[]';

            if (worksContainer) {
                worksContainer.replaceChildren();
                var works = [];
                try {
                    works = JSON.parse(director?.works_raw || '[]');
                } catch (e) {
                    works = [];
                }

                if (works && works.length > 0) {
                    works.forEach(function (work) {
                        addWorkRow(work);
                    });
                } else {
                    addWorkRow(null);
                    addWorkRow(null);
                }
            }

            var currentImgLink = form.querySelector('[data-admin-directors-current-image]');
            var currentImgPreview = form.querySelector('[data-admin-directors-current-image-preview]');
            var currentImgWrapper = form.querySelector('[data-admin-directors-current-image-wrapper]');
            if (currentImgLink) {
                if (director?.bio_image) {
                    currentImgLink.href = director.bio_image;
                    currentImgLink.textContent = director.bio_image;
                    if (currentImgPreview) {
                        currentImgPreview.src = director.bio_image;
                    }
                    if (currentImgWrapper) {
                        currentImgWrapper.classList.remove('hidden');
                    }
                } else {
                    if (currentImgWrapper) {
                        currentImgWrapper.classList.add('hidden');
                    }
                }
            }
            form.elements.bio_image_file.value = '';

            form.elements.stat_1_value.value = director?.stat_1_value || '';
            form.elements.stat_1_suffix.value = director?.stat_1_suffix || '';
            form.elements.stat_1_label.value = director?.stat_1_label || '';
            form.elements.stat_2_value.value = director?.stat_2_value || '';
            form.elements.stat_2_suffix.value = director?.stat_2_suffix || '';
            form.elements.stat_2_label.value = director?.stat_2_label || '';
            form.elements.stat_3_value.value = director?.stat_3_value || '';
            form.elements.stat_3_suffix.value = director?.stat_3_suffix || '';
            form.elements.stat_3_label.value = director?.stat_3_label || '';

            shell.querySelector('[data-admin-directors-form-title]').textContent = isDuplicate
                ? 'Create Director (Duplicate)'
                : (director ? 'Edit Director' : 'Create Director');
            form.elements.first_name.focus();
        }

        function filteredDirectors() {
            var value = (search?.value || '').trim().toLowerCase();

            if (!value) {
                return directors;
            }

            return directors.filter(function (director) {
                return (
                    director.first_name.toLowerCase().includes(value) ||
                    director.last_name.toLowerCase().includes(value) ||
                    director.slug.toLowerCase().includes(value) ||
                    director.role.toLowerCase().includes(value)
                );
            });
        }

        function render() {
            var rows = filteredDirectors();

            table.replaceChildren();

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');

                emptyCell.colSpan = 5;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No directors found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (director) {
                var row = document.createElement('tr');
                var nameCell = document.createElement('td');
                var nameWrap = document.createElement('div');
                var initials = document.createElement('div');
                var name = document.createElement('span');
                var slugCell = document.createElement('td');
                var roleCell = document.createElement('td');
                var created = document.createElement('td');
                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.035]';
                nameCell.className = 'px-5 py-4';
                nameWrap.className = 'flex items-center gap-3';
                initials.className = 'flex size-9 items-center justify-center rounded border border-white/10 bg-white/[0.04] text-xs font-semibold uppercase text-white/75';
                initials.textContent = (director.first_name[0] || '') + (director.last_name[0] || '');
                name.className = 'font-medium text-white';
                name.textContent = director.first_name + ' ' + director.last_name;
                
                slugCell.className = 'px-5 py-4 text-white/48 font-mono text-xs';
                slugCell.textContent = director.slug;

                roleCell.className = 'px-5 py-4 text-white/48 max-w-xs truncate';
                roleCell.textContent = director.role;

                created.className = 'px-5 py-4 text-white/35';
                created.textContent = director.created_at || '';
                
                actions.className = 'px-5 py-4';
                actionsWrap.className = 'flex justify-end gap-2';
                
                edit.type = 'button';
                edit.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                edit.textContent = 'Edit';
                edit.addEventListener('click', function () {
                    openForm(director);
                });
                
                var duplicate = document.createElement('button');
                duplicate.type = 'button';
                duplicate.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                duplicate.textContent = 'Duplicate';
                duplicate.addEventListener('click', function () {
                    openForm(director, true);
                });
                
                remove.type = 'button';
                remove.className = 'rounded border border-[#e60012]/25 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-[#e60012]/55 hover:bg-[#e60012]/12 hover:text-white';
                remove.textContent = 'Delete';
                remove.addEventListener('click', function () {
                    deletingId = director.id;
                    deleteModal?.classList.remove('hidden');
                    deleteModal?.classList.add('grid');
                });

                nameWrap.append(initials, name);
                nameCell.appendChild(nameWrap);
                actionsWrap.append(edit, duplicate, remove);
                actions.appendChild(actionsWrap);
                row.append(nameCell, slugCell, roleCell, created, actions);
                table.appendChild(row);
            });
        }

        function loadDirectors() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            if (search?.value.trim()) {
                url.searchParams.set('search', search.value.trim());
            }

            return request(url.toString(), { cache: 'no-store' }).then(function (payload) {
                directors = payload.directors || [];
                render();
            }).catch(function (error) {
                toast('danger', 'Unable to load directors', error.message || 'Please refresh the page and try again.');
            });
        }

        function checkDuplicateSlug(slug, directorId) {
            var url = new URL(shell.dataset.checkSlugUrl, window.location.origin);

            url.searchParams.set('slug', slug);

            if (directorId) {
                url.searchParams.set('ignore', directorId);
            }

            return request(url.toString()).then(function (payload) {
                return Boolean(payload.exists);
            });
        }

        shell.querySelector('[data-admin-directors-create]')?.addEventListener('click', function () {
            openForm(null);
        });

        shell.querySelector('[data-admin-directors-cancel]')?.addEventListener('click', resetForm);

        var deleteImageModal = shell.querySelector('[data-admin-directors-delete-image-modal]');

        shell.querySelector('[data-admin-directors-delete-image]')?.addEventListener('click', function () {
            if (deleteImageModal) {
                deleteImageModal.classList.remove('hidden');
                deleteImageModal.classList.add('grid');
            }
        });

        shell.querySelector('[data-admin-directors-delete-image-cancel]')?.addEventListener('click', function () {
            if (deleteImageModal) {
                deleteImageModal.classList.add('hidden');
                deleteImageModal.classList.remove('grid');
            }
        });

        shell.querySelector('[data-admin-directors-delete-image-confirm]')?.addEventListener('click', function () {
            form.elements.bio_image.value = '';
            form.elements.bio_image_file.value = '';
            
            var wrapper = shell.querySelector('[data-admin-directors-current-image-wrapper]');
            if (wrapper) {
                wrapper.classList.add('hidden');
            }

            if (deleteImageModal) {
                deleteImageModal.classList.add('hidden');
                deleteImageModal.classList.remove('grid');
            }
        });

        shell.querySelector('[data-admin-directors-delete-work-cancel]')?.addEventListener('click', function () {
            deletingWorkRow = null;
            if (deleteWorkModal) {
                deleteWorkModal.classList.add('hidden');
                deleteWorkModal.classList.remove('grid');
            }
        });

        shell.querySelector('[data-admin-directors-delete-work-confirm]')?.addEventListener('click', function () {
            if (deletingWorkRow) {
                deletingWorkRow.remove();
                renumberWorks();
                deletingWorkRow = null;
            }
            if (deleteWorkModal) {
                deleteWorkModal.classList.add('hidden');
                deleteWorkModal.classList.remove('grid');
            }
        });

        form.elements.bio_image_file?.addEventListener('change', function () {
            var file = this.files[0];
            if (file) {
                var currentImgPreview = form.querySelector('[data-admin-directors-current-image-preview]');
                var currentImgWrapper = form.querySelector('[data-admin-directors-current-image-wrapper]');
                var currentImgLink = form.querySelector('[data-admin-directors-current-image]');

                if (currentImgPreview) {
                    currentImgPreview.src = URL.createObjectURL(file);
                }
                
                if (currentImgLink) {
                    currentImgLink.textContent = file.name + ' (preview)';
                    currentImgLink.removeAttribute('href');
                }

                if (currentImgWrapper) {
                    currentImgWrapper.classList.remove('hidden');
                }
            }
        });

        search?.addEventListener('input', function () {
            render();
            clearTimeout(searchTimer);
            searchTimer = window.setTimeout(loadDirectors, 300);
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            serializeWorks();

            var id = form.querySelector('[data-admin-directors-id]').value;
            var payload = {
                first_name: form.elements.first_name.value.trim(),
                last_name: form.elements.last_name.value.trim(),
                slug: form.elements.slug.value.trim(),
                eyebrow: form.elements.eyebrow.value.trim(),
                role: form.elements.role.value.trim(),
                bio_title_white: form.elements.bio_title_white.value.trim(),
                bio_title_gradient: form.elements.bio_title_gradient.value.trim(),
                bio_image: form.elements.bio_image.value.trim(),
                bio_alt: form.elements.bio_alt.value.trim(),
                works_eyebrow: form.elements.works_eyebrow.value.trim(),
                works_title_white: form.elements.works_title_white.value.trim(),
                works_title_muted: form.elements.works_title_muted.value.trim(),
                bio_raw: form.elements.bio_raw.value.trim(),
                works_raw: form.elements.works_raw.value.trim(),
                stat_1_value: form.elements.stat_1_value.value.trim(),
                stat_1_suffix: form.elements.stat_1_suffix.value.trim(),
                stat_1_label: form.elements.stat_1_label.value.trim(),
                stat_2_value: form.elements.stat_2_value.value.trim(),
                stat_2_suffix: form.elements.stat_2_suffix.value.trim(),
                stat_2_label: form.elements.stat_2_label.value.trim(),
                stat_3_value: form.elements.stat_3_value.value.trim(),
                stat_3_suffix: form.elements.stat_3_suffix.value.trim(),
                stat_3_label: form.elements.stat_3_label.value.trim(),
            };
            
            var hasFile = Boolean(form.elements.bio_image_file.files.length);
            var validationErrors = validateDirectorPayload(payload, hasFile);
            var url = id ? directorUrl(shell.dataset.updateUrlTemplate, id) : shell.dataset.storeUrl;

            if (Object.keys(validationErrors).length) {
                showErrors(validationErrors);
                toast('danger', 'Please check the form', 'Some profile details need attention.');
                return;
            }

            setBusy(true);

            checkDuplicateSlug(payload.slug, id).then(function (exists) {
                if (exists) {
                    showErrors({
                        slug: ['This slug is already in use.'],
                    });
                    toast('danger', 'Please check the form', 'This slug is already in use.');
                    return null;
                }

                var formData = new FormData(form);
                if (id) {
                    formData.append('_method', 'PATCH');
                }

                return request(url, {
                    method: 'POST', // File uploads in Laravel patch requests require POST method with _method=PATCH
                    body: formData,
                });
            }).then(function (response) {
                if (!response) {
                    return null;
                }

                resetForm();
                toast('success', 'Success', response.message || 'Director saved.');
                return loadDirectors();
            }).catch(function (error) {
                showErrors(error.errors || {});
                toast('danger', 'Unable to save', error.message || 'Please check the form.');
            }).finally(function () {
                setBusy(false);
            });
        });

        form.elements.slug.addEventListener('blur', function () {
            var id = form.querySelector('[data-admin-directors-id]').value;
            var slug = form.elements.slug.value.trim();

            if (!slug || !/^[a-z0-9-_]+$/.test(slug)) {
                return;
            }

            checkDuplicateSlug(slug, id).then(function (exists) {
                if (!exists) {
                    return;
                }

                showErrors({
                    slug: ['This slug is already in use.'],
                });
            }).catch(function () {});
        });

        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-_]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        function updateAutoSlug() {
            var slugVal = form.elements.slug.value.trim();
            if (isSlugManuallyEdited && slugVal !== '') {
                return;
            }
            var first = form.elements.first_name.value || '';
            var last = form.elements.last_name.value || '';
            var combined = (first + ' ' + last).trim();
            form.elements.slug.value = slugify(combined);
        }

        form.elements.first_name?.addEventListener('input', updateAutoSlug);
        form.elements.last_name?.addEventListener('input', updateAutoSlug);
        form.elements.slug?.addEventListener('input', function () {
            isSlugManuallyEdited = true;
        });


        shell.querySelector('[data-admin-directors-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) {
                return;
            }

            var deleteSpinner = deleteModal?.querySelector('[data-admin-directors-delete-spinner]');
            var deleteLabel = deleteModal?.querySelector('[data-admin-directors-delete-label]');

            deleteConfirm.disabled = true;
            if (deleteSpinner) deleteSpinner.classList.remove('hidden');
            if (deleteLabel) deleteLabel.textContent = 'Deleting...';

            request(directorUrl(shell.dataset.deleteUrlTemplate, deletingId), {
                method: 'DELETE',
            }).then(function (response) {
                directors = directors.filter(function (director) {
                    return director.id !== deletingId;
                });
                render();
                toast('success', 'Success', response.message || 'Director deleted.');
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');

                return loadDirectors().catch(function () {});
            }).catch(function (error) {
                toast('danger', 'Unable to delete', error.message || 'Please try again.');
            }).finally(function () {
                deleteConfirm.disabled = false;
                if (deleteSpinner) deleteSpinner.classList.add('hidden');
                if (deleteLabel) deleteLabel.textContent = 'Delete';
            });
        });

        render();
        loadDirectors();
    });
}

function initAdminFaqs() {
    document.querySelectorAll('[data-admin-faqs]:not([data-admin-faqs-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-faqs-initial]');
        var table = shell.querySelector('[data-admin-faqs-table]');
        var formShell = shell.querySelector('[data-admin-faqs-form-shell]');
        var form = shell.querySelector('[data-admin-faqs-form]');
        var search = shell.querySelector('[data-admin-faqs-search]');
        var categoryFilters = shell.querySelector('[data-admin-faqs-category-filters]');
        var deleteModal = shell.querySelector('[data-admin-faqs-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-faqs-delete-confirm]');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var faqs = [];
        var deletingId = null;
        var selectedCategory = '';
        var faqSortable = null;

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-faqs-ready', '');

        try {
            faqs = JSON.parse(initialData?.textContent || '[]');
        } catch (error) {
            faqs = [];
        }

        var categorySelect = form.querySelector('[data-admin-faqs-category-select]');
        var categoryInput = form.querySelector('[data-admin-faqs-field="category"]');
        var defaultCategories = [
            'Workflow & Timeline',
            'Quality & Scalability',
            'Data Security & Brand Identity'
        ];

        function populateCategoryDropdown(selectedCategory) {
            if (!categorySelect) return;

            var uniqueCategories = new Set(defaultCategories);
            faqs.forEach(function (faq) {
                if (faq.category && faq.category.trim()) {
                    uniqueCategories.add(faq.category.trim());
                }
            });

            var currentVal = selectedCategory || categoryInput.value || 'Workflow & Timeline';

            categorySelect.replaceChildren();

            uniqueCategories.forEach(function (cat) {
                var option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categorySelect.appendChild(option);
            });

            var customOption = document.createElement('option');
            customOption.value = '__NEW__';
            customOption.textContent = '+ Add Custom Category...';
            categorySelect.appendChild(customOption);

            if (uniqueCategories.has(currentVal)) {
                categorySelect.value = currentVal;
                categoryInput.value = currentVal;
                categoryInput.classList.add('hidden');
            } else {
                categorySelect.value = '__NEW__';
                categoryInput.value = currentVal;
                categoryInput.classList.remove('hidden');
            }
        }

        categorySelect?.addEventListener('change', function () {
            if (categorySelect.value === '__NEW__') {
                categoryInput.value = '';
                categoryInput.classList.remove('hidden');
                categoryInput.focus();
            } else {
                categoryInput.value = categorySelect.value;
                categoryInput.classList.add('hidden');
                var errorElement = shell.querySelector('[data-admin-faqs-error="category"]');
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.add('hidden');
                }
                categoryInput.classList.remove('border-red-500', 'focus:border-red-500');
                categoryInput.classList.add('border-white/10');
            }
        });

        function faqUrl(template, faqId) {
            return template.replace('__FAQ__', faqId);
        }

        function request(url, options) {
            var headers = {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            };
            if (options && options.body && !(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
            return fetch(url, Object.assign({
                headers: headers
            }, options || {})).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }
                    return payload;
                });
            });
        }

        function toast(variant, heading, text) {
            showToast(variant, heading, text);
        }

        function setBusy(busy) {
            var save = shell.querySelector('[data-admin-faqs-save]');
            var spinner = shell.querySelector('[data-admin-faqs-spinner]');
            var label = shell.querySelector('[data-admin-faqs-save-label]');

            if (save) {
                save.disabled = busy;
            }

            spinner?.classList.toggle('hidden', !busy);

            if (label) {
                label.textContent = busy ? 'Saving...' : 'Save FAQ';
            }
        }

        function clearErrors() {
            shell.querySelectorAll('[data-admin-faqs-error]').forEach(function (error) {
                error.textContent = '';
                error.classList.add('hidden');
            });

            shell.querySelectorAll('[data-admin-faqs-field]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });
        }

        function showErrors(errors) {
            Object.entries(errors || {}).forEach(function ([name, messages]) {
                var error = shell.querySelector('[data-admin-faqs-error="' + name + '"]');
                var field = shell.querySelector('[data-admin-faqs-field="' + name + '"]');

                if (error) {
                    error.textContent = Array.isArray(messages) ? messages[0] : messages;
                    error.classList.remove('hidden');
                }

                if (field) {
                    field.classList.remove('border-white/10');
                    field.classList.add('border-red-500', 'focus:border-red-500');
                }
            });
        }

        function validateFaqPayload(payload) {
            var errors = {};

            if (!payload.category.trim()) {
                errors.category = ['Please select a category.'];
            }
            if (!payload.question.trim()) {
                errors.question = ['Please enter a question.'];
            }
            if (!payload.answer.trim()) {
                errors.answer = ['Please enter an answer.'];
            }
            if (payload.sort_order === '' || isNaN(payload.sort_order)) {
                errors.sort_order = ['Please enter a valid sort order number.'];
            }

            return errors;
        }

        function initDragAndDrop() {
            if (faqSortable) {
                faqSortable.destroy();
            }

            faqSortable = Sortable.create(table, {
                handle: '[data-admin-faqs-drag-handle]',
                draggable: '[data-admin-faqs-row]',
                animation: 180,
                ghostClass: 'opacity-25',
                chosenClass: 'bg-white/[0.055]',
                dragClass: 'shadow-2xl',
                delay: 120,
                delayOnTouchOnly: true,
                touchStartThreshold: 4,
                fallbackTolerance: 3,
                fallbackOnBody: true,
                onEnd: function (event) {
                    if (event.oldIndex !== event.newIndex) {
                        saveNewOrder();
                    }
                },
            });
        }

        function saveNewOrder() {
            var ids = Array.from(table.querySelectorAll('[data-faq-id]')).map(function (row) {
                return parseInt(row.getAttribute('data-faq-id'), 10);
            }).filter(Boolean);

            if (!ids.length) return;

            request(shell.dataset.reorderUrl, {
                method: 'PATCH',
                body: JSON.stringify({ ids: ids }),
            }).then(function (response) {
                toast('success', 'Success', response.message || 'FAQ order updated.');
                return loadFaqs();
            }).catch(function (error) {
                toast('danger', 'Unable to reorder', error.message || 'Please try again.');
                loadFaqs();
            });
        }

        function renderCategoryFilters() {
            if (!categoryFilters) return;

            var categories = Array.from(new Set(faqs.map(function (faq) {
                return faq.category;
            }).filter(Boolean))).sort();

            if (selectedCategory && !categories.includes(selectedCategory)) {
                selectedCategory = '';
            }

            categoryFilters.replaceChildren();

            [['', 'All Groups']].concat(categories.map(function (category) {
                return [category, category];
            })).forEach(function (item) {
                var button = document.createElement('button');
                var active = selectedCategory === item[0];
                var count = item[0]
                    ? faqs.filter(function (faq) { return faq.category === item[0]; }).length
                    : faqs.length;

                button.type = 'button';
                button.className = active
                    ? 'rounded border border-[#366bc3] bg-[#366bc3]/10 px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white'
                    : 'rounded border border-white/10 px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-white/42 transition hover:border-white/25 hover:text-white';
                button.textContent = item[1] + ' (' + count + ')';
                button.addEventListener('click', function () {
                    selectedCategory = item[0];
                    renderCategoryFilters();
                    render();
                });
                categoryFilters.appendChild(button);
            });
        }

        function resetForm() {
            form.reset();
            clearErrors();
            form.querySelector('[data-admin-faqs-id]').value = '';
            shell.querySelector('[data-admin-faqs-form-title]').textContent = 'Create FAQ';
            categoryInput?.classList.add('hidden');
            form.elements.keywords.value = '';
            formShell.classList.add('hidden');
        }

        function openForm(faq) {
            clearErrors();
            formShell.classList.remove('hidden');
            form.querySelector('[data-admin-faqs-id]').value = faq?.id || '';
            var categoryVal = faq?.category || 'Workflow & Timeline';
            populateCategoryDropdown(categoryVal);
            form.elements.question.value = faq?.question || '';
            form.elements.answer.value = faq?.answer || '';
            form.elements.keywords.value = faq?.keywords || '';
            form.elements.sort_order.value = faq
                ? faq.sort_order
                : (faqs.length ? Math.max.apply(Math, faqs.map(function (item) {
                    return Number(item.sort_order) || 0;
                })) + 10 : 10);
            shell.querySelector('[data-admin-faqs-form-title]').textContent = faq ? 'Edit FAQ' : 'Create FAQ';
            form.elements.question.focus();
        }

        function filteredFaqs() {
            var value = (search?.value || '').trim().toLowerCase();
            return faqs.filter(function (faq) {
                var matchesCategory = !selectedCategory || faq.category === selectedCategory;
                var matchesSearch = !value || (
                    faq.category.toLowerCase().includes(value) ||
                    faq.question.toLowerCase().includes(value) ||
                    faq.answer.toLowerCase().includes(value) ||
                    (faq.keywords && faq.keywords.toLowerCase().includes(value))
                );

                return matchesCategory && matchesSearch;
            });
        }

        function render() {
            var rows = filteredFaqs();
            var hasSearch = Boolean((search?.value || '').trim());

            table.replaceChildren();

            if (faqSortable) {
                faqSortable.option('disabled', hasSearch);
            }

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');

                emptyCell.colSpan = 6;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No FAQs found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (faq) {
                var row = document.createElement('tr');
                var dragCell = document.createElement('td');
                var categoryCell = document.createElement('td');
                var questionCell = document.createElement('td');
                var answerCell = document.createElement('td');
                var orderCell = document.createElement('td');
                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.035]';
                row.setAttribute('data-admin-faqs-row', '');
                row.setAttribute('data-faq-id', faq.id);

                dragCell.className = 'w-16 px-3 py-4 text-center';
                var dragHandle = document.createElement('button');
                dragHandle.type = 'button';
                dragHandle.className = 'inline-flex size-9 touch-none select-none items-center justify-center rounded border border-transparent text-white/35 transition hover:border-white/10 hover:bg-white/[0.06] hover:text-white/75 disabled:cursor-not-allowed disabled:opacity-20';
                dragHandle.setAttribute('data-admin-faqs-drag-handle', '');
                dragHandle.setAttribute('aria-label', 'Drag FAQ to reorder');
                dragHandle.title = selectedCategory ? 'Drag to reorder within this group' : 'Drag to reorder all FAQs';
                dragHandle.disabled = hasSearch;
                dragHandle.innerHTML = '<svg class="size-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6h.01M15 6h.01M9 12h.01M15 12h.01M9 18h.01M15 18h.01" /></svg>';
                dragCell.appendChild(dragHandle);
                categoryCell.className = 'px-5 py-4 font-semibold text-white/70';
                categoryCell.textContent = faq.category;

                questionCell.className = 'px-5 py-4 font-medium text-white max-w-xs';
                questionCell.replaceChildren();
                var qText = document.createElement('div');
                qText.className = 'font-medium text-white truncate';
                qText.textContent = faq.question;
                questionCell.appendChild(qText);

                if (faq.keywords) {
                    var kwText = document.createElement('div');
                    kwText.className = 'text-[11px] text-white/35 mt-1 font-normal tracking-wide truncate';
                    kwText.textContent = 'SEO: ' + faq.keywords;
                    questionCell.appendChild(kwText);
                }

                answerCell.className = 'px-5 py-4 text-white/48 max-w-sm truncate';
                answerCell.textContent = faq.answer;

                orderCell.className = 'px-5 py-4 text-white/60 font-mono';
                orderCell.textContent = faq.sort_order;

                actions.className = 'px-5 py-4';
                actionsWrap.className = 'flex justify-end gap-2';

                edit.type = 'button';
                edit.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                edit.textContent = 'Edit';
                edit.addEventListener('click', function () {
                    openForm(faq);
                });

                remove.type = 'button';
                remove.className = 'rounded border border-[#e60012]/25 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-[#e60012]/55 hover:bg-[#e60012]/12 hover:text-white';
                remove.textContent = 'Delete';
                remove.addEventListener('click', function () {
                    deletingId = faq.id;
                    deleteModal?.classList.remove('hidden');
                    deleteModal?.classList.add('grid');
                });

                actionsWrap.append(edit, remove);
                actions.appendChild(actionsWrap);
                row.append(dragCell, categoryCell, questionCell, answerCell, orderCell, actions);
                table.appendChild(row);
            });
        }

        function loadFaqs() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            return request(url.toString(), { cache: 'no-store' }).then(function (payload) {
                faqs = payload.faqs || [];
                populateCategoryDropdown();
                renderCategoryFilters();
                render();
            }).catch(function (error) {
                toast('danger', 'Unable to load FAQs', error.message || 'Please refresh the page and try again.');
            });
        }

        shell.querySelector('[data-admin-faqs-create]')?.addEventListener('click', function () {
            openForm(null);
        });

        shell.querySelector('[data-admin-faqs-cancel]')?.addEventListener('click', resetForm);

        search?.addEventListener('input', function () {
            render();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            var id = form.querySelector('[data-admin-faqs-id]').value;
            var payload = {
                category: form.elements.category.value,
                question: form.elements.question.value.trim(),
                answer: form.elements.answer.value.trim(),
                keywords: form.elements.keywords.value.trim(),
                sort_order: parseInt(form.elements.sort_order.value, 10),
            };

            var validationErrors = validateFaqPayload(payload);
            var url = id ? faqUrl(shell.dataset.updateUrlTemplate, id) : shell.dataset.storeUrl;
            var method = id ? 'PATCH' : 'POST';

            if (Object.keys(validationErrors).length) {
                showErrors(validationErrors);
                toast('danger', 'Please check the form', 'Some FAQ details need attention.');
                return;
            }

            setBusy(true);

            request(url, {
                method: method,
                body: JSON.stringify(payload),
            }).then(function (response) {
                resetForm();
                toast('success', 'Success', response.message || 'FAQ saved.');
                return loadFaqs();
            }).catch(function (error) {
                showErrors(error.errors || {});
                toast('danger', 'Unable to save', error.message || 'Please check the form.');
            }).finally(function () {
                setBusy(false);
            });
        });

        shell.querySelector('[data-admin-faqs-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) {
                return;
            }

            deleteConfirm.disabled = true;

            request(faqUrl(shell.dataset.deleteUrlTemplate, deletingId), {
                method: 'DELETE',
            }).then(function (response) {
                toast('success', 'Success', response.message || 'FAQ deleted.');
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');
                return loadFaqs();
            }).catch(function (error) {
                toast('danger', 'Unable to delete', error.message || 'Please try again.');
            }).finally(function () {
                deleteConfirm.disabled = false;
            });
        });

        initDragAndDrop();
        populateCategoryDropdown();
        renderCategoryFilters();
        render();
        loadFaqs();
    });
}

function initAdminServices() {
    document.querySelectorAll('[data-admin-services]:not([data-admin-services-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-services-initial]');
        var table = shell.querySelector('[data-admin-services-table]');
        var formShell = shell.querySelector('[data-admin-services-form-shell]');
        var form = shell.querySelector('[data-admin-services-form]');
        var search = shell.querySelector('[data-admin-services-search]');
        var deleteModal = shell.querySelector('[data-admin-services-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-services-delete-confirm]');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var services = [];
        var deletingId = null;
        var searchTimer;
        var sortable = null;

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-services-ready', '');

        try {
            services = JSON.parse(initialData?.textContent || '[]');
        } catch (error) {
            services = [];
        }

        function serviceUrl(template, serviceId) {
            return template.replace('__SERVICE__', serviceId);
        }

        function request(url, options) {
            var headers = {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            };
            if (options && options.body && !(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
            return fetch(url, Object.assign({
                headers: headers
            }, options || {})).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }
                    return payload;
                });
            });
        }

        function toast(variant, heading, text) {
            showToast(variant, heading, text);
        }

        function setBusy(busy) {
            var save = shell.querySelector('[data-admin-services-save]');
            var spinner = shell.querySelector('[data-admin-services-spinner]');
            var label = shell.querySelector('[data-admin-services-save-label]');

            if (save) {
                save.disabled = busy;
            }

            spinner?.classList.toggle('hidden', !busy);

            if (label) {
                label.textContent = busy ? 'Saving...' : 'Save Service';
            }
        }

        function clearErrors() {
            shell.querySelectorAll('[data-admin-services-error]').forEach(function (error) {
                error.textContent = '';
                error.classList.add('hidden');
            });

            shell.querySelectorAll('[data-admin-services-field]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });
        }

        function showErrors(errors) {
            Object.entries(errors || {}).forEach(function ([name, messages]) {
                var error = shell.querySelector('[data-admin-services-error="' + name + '"]');
                var field = shell.querySelector('[data-admin-services-field="' + name + '"]');

                if (error) {
                    error.textContent = Array.isArray(messages) ? messages[0] : messages;
                    error.classList.remove('hidden');
                }

                if (field) {
                    field.classList.remove('border-white/10');
                    field.classList.add('border-red-500', 'focus:border-red-500');
                }
            });
        }

        function validateServicePayload(payload, hasFile) {
            var errors = {};

            if (!payload.num.trim()) {
                errors.num = ['Please enter service number (e.g. 01).'];
            }
            if (!payload.title.trim()) {
                errors.title = ['Please enter a title.'];
            }
            if (!payload.description.trim()) {
                errors.description = ['Please enter a description.'];
            }
            if (!payload.bullets_raw.trim()) {
                errors.bullets_raw = ['Please enter at least one bullet point.'];
            }
            if (!payload.accent.trim()) {
                errors.accent = ['Please enter an accent color hex code.'];
            }
            if (!payload.sort_order.trim() || isNaN(parseInt(payload.sort_order))) {
                errors.sort_order = ['Please enter a valid sort order.'];
            }

            return errors;
        }

        function initDragAndDrop() {
            if (!table) return;
            if (sortable) {
                sortable.destroy();
            }

            sortable = Sortable.create(table, {
                handle: '[data-admin-services-drag-handle]',
                draggable: '[data-admin-services-row]',
                animation: 180,
                ghostClass: 'opacity-25',
                chosenClass: 'bg-white/[0.055]',
                dragClass: 'shadow-2xl',
                delay: 120,
                delayOnTouchOnly: true,
                touchStartThreshold: 4,
                fallbackTolerance: 3,
                fallbackOnBody: true,
                onStart: function () {
                    shell.setAttribute('data-admin-services-sorting', '');
                },
                onEnd: function (event) {
                    shell.removeAttribute('data-admin-services-sorting');
                    if (event.oldIndex !== event.newIndex) {
                        saveNewOrder();
                    }
                },
            });
        }

        function saveNewOrder() {
            var ids = [];
            table.querySelectorAll('[data-service-id]').forEach(function (row) {
                var id = parseInt(row.getAttribute('data-service-id'));
                if (id) {
                    ids.push(id);
                }
            });

            if (!ids.length) return;

            setBusy(true);

            request(shell.dataset.reorderUrl, {
                method: 'PATCH',
                body: JSON.stringify({ ids: ids }),
            }).then(function (res) {
                toast('success', 'Reorder successful', res.message);
                loadServices();
            }).catch(function (error) {
                toast('danger', 'Reorder failed', error.message || 'Unable to save new order.');
                loadServices();
            }).finally(function () {
                setBusy(false);
            });
        }

        function render() {
            table.replaceChildren();

            var searchVal = (search?.value || '').toLowerCase().trim();
            var rows = services;

            if (sortable) {
                sortable.option('disabled', Boolean(searchVal));
            }

            if (searchVal) {
                rows = services.filter(function (s) {
                    return (s.title || '').toLowerCase().indexOf(searchVal) > -1 ||
                        (s.description || '').toLowerCase().indexOf(searchVal) > -1 ||
                        (s.num || '').toLowerCase().indexOf(searchVal) > -1;
                });
            }

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');
                emptyCell.colSpan = 7;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No services found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (service) {
                var row = document.createElement('tr');
                row.setAttribute('data-admin-services-row', '');
                row.setAttribute('data-service-id', service.id);

                var dragCell = document.createElement('td');
                dragCell.className = 'px-3 py-2 w-16 text-center';
                dragCell.innerHTML = '<button type="button" class="inline-flex size-10 touch-none select-none items-center justify-center rounded-md border border-transparent text-white/35 transition hover:border-white/10 hover:bg-white/[0.06] hover:text-white/75 active:cursor-grabbing active:bg-white/10 cursor-grab" title="Drag to reorder" aria-label="Drag service to reorder" data-admin-services-drag-handle>' +
                    '<svg class="size-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 6h.01M15 6h.01M9 12h.01M15 12h.01M9 18h.01M15 18h.01" />' +
                    '</svg>' +
                    '</button>';

                var imageCell = document.createElement('td');
                var imageWrap = document.createElement('div');
                var numCell = document.createElement('td');
                var titleCell = document.createElement('td');
                var descCell = document.createElement('td');
                var orderCell = document.createElement('td');
                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.035]';
                imageCell.className = 'px-5 py-4 w-24';
                imageWrap.className = 'relative aspect-video w-16 overflow-hidden border border-white/10 bg-black rounded';
                if (service.image) {
                    var img = document.createElement('img');
                    img.src = service.image;
                    img.className = 'h-full w-full object-cover';
                    img.draggable = false;
                    imageWrap.appendChild(img);
                } else {
                    var imagePlaceholder = document.createElement('div');
                    imagePlaceholder.className = 'flex h-full w-full items-center justify-center text-white/30';
                    imagePlaceholder.style.backgroundColor = service.accent || '#366bc3';
                    imagePlaceholder.innerHTML = '<div class="absolute inset-0 bg-black/55"></div>' +
                        '<svg class="relative size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />' +
                        '</svg>';
                    imageWrap.appendChild(imagePlaceholder);
                }
                imageCell.appendChild(imageWrap);

                numCell.className = 'px-5 py-4 text-white/48 font-mono text-xs w-16';
                numCell.textContent = service.num;

                titleCell.className = 'px-5 py-4 font-semibold text-white w-1/4';
                titleCell.textContent = service.title;

                descCell.className = 'px-5 py-4 text-white/48 max-w-sm truncate';
                descCell.textContent = service.description;

                orderCell.className = 'px-5 py-4 text-white/35 font-mono text-xs w-20';
                orderCell.textContent = service.sort_order;

                actions.className = 'px-5 py-4';
                actionsWrap.className = 'flex justify-end gap-2';

                edit.type = 'button';
                edit.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                edit.textContent = 'Edit';
                edit.addEventListener('click', function () {
                    openForm(service);
                });

                remove.type = 'button';
                remove.className = 'rounded border border-[#e60012]/25 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-[#e60012]/55 hover:bg-[#e60012]/12 hover:text-white';
                remove.textContent = 'Delete';
                remove.addEventListener('click', function () {
                    deletingId = service.id;
                    deleteModal?.classList.remove('hidden');
                    deleteModal?.classList.add('grid');
                });

                actionsWrap.append(edit, remove);
                actions.appendChild(actionsWrap);
                row.append(dragCell, imageCell, numCell, titleCell, descCell, orderCell, actions);
                table.appendChild(row);
            });
        }

        function loadServices() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            return request(url.toString(), { cache: 'no-store' }).then(function (payload) {
                services = payload.services || [];
                render();
            }).catch(function (error) {
                toast('danger', 'Unable to load services', error.message || 'Please refresh the page and try again.');
            });
        }

        shell.querySelector('[data-admin-services-create]')?.addEventListener('click', function () {
            openForm(null);
        });

        shell.querySelector('[data-admin-services-cancel]')?.addEventListener('click', resetForm);

        var uploadCard = form.querySelector('[data-admin-services-upload-card]');
        var fileInput = form.querySelector('[data-admin-services-file-input]');
        var previewWrapper = form.querySelector('[data-admin-services-preview-wrapper]');
        var previewImg = form.querySelector('[data-admin-services-preview]');
        var uploadPlaceholder = form.querySelector('[data-admin-services-upload-placeholder]');
        var removeImageBtn = form.querySelector('[data-admin-services-remove-image-file]');

        function syncUploadPlaceholderAccent() {
            if (!uploadCard) return;
            var accent = form.elements.accent?.value.trim() || '#366bc3';
            uploadCard.style.background = 'linear-gradient(rgba(0, 0, 0, .58), rgba(0, 0, 0, .58)), ' + accent;
        }

        form.elements.accent?.addEventListener('input', syncUploadPlaceholderAccent);

        if (uploadCard && fileInput) {
            uploadCard.addEventListener('click', function () {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                var file = this.files[0];
                if (file) {
                    if (previewImg) previewImg.src = URL.createObjectURL(file);
                    if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                    if (previewWrapper) previewWrapper.classList.remove('hidden');
                    var imgField = form.querySelector('[data-admin-services-field="image"]');
                    if (imgField) imgField.value = '';
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (fileInput) fileInput.value = '';
                var imgField = form.querySelector('[data-admin-services-field="image"]');
                if (imgField) imgField.value = '';
                if (previewWrapper) previewWrapper.classList.add('hidden');
                if (previewImg) previewImg.src = '';
                if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
            });
        }

        search?.addEventListener('input', function () {
            render();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            var id = form.querySelector('[data-admin-services-id]').value;
            var payload = {
                num: form.elements.num.value.trim(),
                title: form.elements.title.value.trim(),
                description: form.elements.description.value.trim(),
                bullets_raw: form.elements.bullets_raw.value.trim(),
                accent: form.elements.accent.value.trim(),
                sort_order: form.elements.sort_order.value.trim(),
            };

            var hasFile = Boolean(fileInput?.files.length);
            var validationErrors = validateServicePayload(payload, hasFile);
            var url = id ? serviceUrl(shell.dataset.updateUrlTemplate, id) : shell.dataset.storeUrl;

            if (Object.keys(validationErrors).length) {
                showErrors(validationErrors);
                toast('danger', 'Please check the form', 'Some details need attention.');
                return;
            }

            setBusy(true);

            var formData = new FormData(form);
            if (id) {
                formData.append('_method', 'PATCH');
            }

            request(url, {
                method: 'POST',
                body: formData,
            }).then(function (res) {
                toast('success', id ? 'Service updated' : 'Service created', res.message);
                resetForm();
                loadServices();
            }).catch(function (err) {
                if (err.errors) {
                    showErrors(err.errors);
                    toast('danger', 'Save failed', 'Please correct the validation errors.');
                } else {
                    toast('danger', 'Save failed', err.message || 'An error occurred while saving.');
                }
            }).finally(function () {
                setBusy(false);
            });
        });

        shell.querySelector('[data-admin-services-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) return;

            setBusy(true);
            var url = serviceUrl(shell.dataset.deleteUrlTemplate, deletingId);

            request(url, {
                method: 'DELETE',
            }).then(function (res) {
                toast('success', 'Service deleted', res.message);
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');
                loadServices();
            }).catch(function (err) {
                toast('danger', 'Deletion failed', err.message || 'Unable to delete service.');
            }).finally(function () {
                setBusy(false);
            });
        });

        function resetForm() {
            form.reset();
            form.querySelector('[data-admin-services-id]').value = '';
            if (fileInput) fileInput.value = '';
            if (previewWrapper) previewWrapper.classList.add('hidden');
            if (previewImg) previewImg.src = '';
            if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
            clearErrors();
            formShell.classList.add('hidden');
        }

        function openForm(service) {
            resetForm();
            formShell.classList.remove('hidden');

            shell.querySelector('[data-admin-services-form-title]').textContent = service ? 'Edit Service' : 'Create Service';

            if (service) {
                form.querySelector('[data-admin-services-id]').value = service.id;
                form.elements.num.value = service.num || '';
                form.elements.title.value = service.title || '';
                form.elements.description.value = service.description || '';
                form.elements.bullets_raw.value = service.bullets_raw || '';
                form.elements.accent.value = service.accent || '#366bc3';
                form.elements.sort_order.value = service.sort_order ?? 0;
                var imgField = form.querySelector('[data-admin-services-field="image"]');
                if (imgField) imgField.value = service.image || '';

                if (service.image) {
                    if (previewImg) previewImg.src = service.image;
                    if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                    if (previewWrapper) previewWrapper.classList.remove('hidden');
                }
            } else {
                form.elements.accent.value = '#366bc3';
                form.elements.sort_order.value = (services.length ? Math.max.apply(Math, services.map(function(s){return s.sort_order;})) + 10 : 10);
            }
            syncUploadPlaceholderAccent();
            form.elements.num.focus();
        }

        initDragAndDrop();
        render();

        // Livewire may restore a page that was prefetched before a create/update/delete.
        // Always reconcile the embedded snapshot with the current database state.
        loadServices();
    });
}

function initAdminPortfolios() {
    document.querySelectorAll('[data-admin-portfolios]:not([data-admin-portfolios-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-portfolios-initial]');
        var table = shell.querySelector('[data-admin-portfolios-table]');
        var formShell = shell.querySelector('[data-admin-portfolios-form-shell]');
        var form = shell.querySelector('[data-admin-portfolios-form]');
        var search = shell.querySelector('[data-admin-portfolios-search]');
        var deleteModal = shell.querySelector('[data-admin-portfolios-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-portfolios-delete-confirm]');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var portfolios = [];
        var services = [];
        var deletingId = null;
        var searchTimer;
        var selectedServiceId = '';
        var portfolioSortable = null;

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-portfolios-ready', '');

        var tabsContainer = shell.querySelector('[data-admin-portfolios-tabs]');
        var serviceSelect = shell.querySelector('[data-admin-portfolios-field="service_id"]');

        function updateTabStyles() {
            shell.querySelectorAll('[data-admin-portfolios-tab]').forEach(function (btn) {
                var id = btn.getAttribute('data-admin-portfolios-tab') || '';
                if (id === (selectedServiceId + '')) {
                    if (id === '') {
                        btn.className = 'rounded border px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider transition-all duration-200 border-[#366bc3] bg-[#366bc3]/10 text-white';
                        btn.style.borderColor = '';
                        btn.style.backgroundColor = '';
                        btn.style.color = '';
                        btn.style.boxShadow = '';
                    } else {
                        var accent = btn.getAttribute('data-accent') || '#366bc3';
                        btn.className = 'rounded border px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider transition-all duration-200 text-white';
                        btn.style.borderColor = accent;
                        btn.style.backgroundColor = accent + '18';
                        btn.style.boxShadow = '0 10px 15px -3px ' + accent + '20';
                    }
                } else {
                    btn.className = 'rounded border px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider transition-all duration-200 border-white/10 text-white/42 hover:border-white/25 hover:text-white';
                    btn.style.borderColor = '';
                    btn.style.backgroundColor = '';
                    btn.style.color = '';
                    btn.style.boxShadow = '';
                }
            });
        }

        function bindTabEvents() {
            shell.querySelectorAll('[data-admin-portfolios-tab]').forEach(function (tabBtn) {
                tabBtn.addEventListener('click', function () {
                    selectedServiceId = tabBtn.getAttribute('data-admin-portfolios-tab') || '';
                    updateTabStyles();
                    render();
                });
            });
        }

        function renderServices() {
            if (!tabsContainer || !serviceSelect) return;

            // 1. Re-render select options
            var selectedVal = serviceSelect.value;
            serviceSelect.replaceChildren();
            
            var defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'None / General';
            serviceSelect.appendChild(defaultOpt);

            services.forEach(function (service) {
                var opt = document.createElement('option');
                opt.value = service.id;
                opt.textContent = service.title;
                serviceSelect.appendChild(opt);
            });
            serviceSelect.value = selectedVal;

            // 2. Re-render tabs
            tabsContainer.replaceChildren();

            var allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.setAttribute('data-admin-portfolios-tab', '');
            allBtn.textContent = 'All Services (' + portfolios.length + ')';
            tabsContainer.appendChild(allBtn);

            services.forEach(function (service) {
                var btn = document.createElement('button');
                var count = portfolios.filter(function (portfolio) {
                    return String(portfolio.service_id || '') === String(service.id);
                }).length;
                btn.type = 'button';
                btn.setAttribute('data-admin-portfolios-tab', service.id);
                btn.setAttribute('data-accent', service.accent || '');
                btn.textContent = service.title + ' (' + count + ')';
                tabsContainer.appendChild(btn);
            });

            // 3. Re-bind click events & styles
            bindTabEvents();
            updateTabStyles();
        }

        try {
            portfolios = JSON.parse(initialData?.textContent || '[]');
        } catch (error) {
            portfolios = [];
        }

        try {
            services = JSON.parse(shell.querySelector('[data-admin-services-initial]')?.textContent || '[]');
        } catch (error) {
            services = [];
        }

        bindTabEvents();
        updateTabStyles();

        function portfolioUrl(template, portfolioId) {
            return template.replace('__PORTFOLIO__', portfolioId);
        }

        function request(url, options) {
            var headers = {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            };
            if (options && options.body && !(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
            return fetch(url, Object.assign({
                headers: headers
            }, options || {})).then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (payload) {
                    if (!response.ok) {
                        throw payload;
                    }
                    return payload;
                });
            });
        }

        function toast(variant, heading, text) {
            showToast(variant, heading, text);
        }

        function setBusy(busy) {
            var save = shell.querySelector('[data-admin-portfolios-save]');
            var spinner = shell.querySelector('[data-admin-portfolios-spinner]');
            var label = shell.querySelector('[data-admin-portfolios-save-label]');

            if (save) {
                save.disabled = busy;
            }

            spinner?.classList.toggle('hidden', !busy);

            if (label) {
                label.textContent = busy ? 'Saving...' : 'Save Portfolio Item';
            }
        }

        function clearErrors() {
            shell.querySelectorAll('[data-admin-portfolios-error]').forEach(function (error) {
                error.textContent = '';
                error.classList.add('hidden');
            });

            shell.querySelectorAll('[data-admin-portfolios-field]').forEach(function (field) {
                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-white/10');
            });
        }

        function showErrors(errors) {
            Object.entries(errors || {}).forEach(function ([name, messages]) {
                var error = shell.querySelector('[data-admin-portfolios-error="' + name + '"]');
                var field = shell.querySelector('[data-admin-portfolios-field="' + name + '"]');

                if (error) {
                    error.textContent = Array.isArray(messages) ? messages[0] : messages;
                    error.classList.remove('hidden');
                }

                if (field) {
                    field.classList.remove('border-white/10');
                    field.classList.add('border-red-500', 'focus:border-red-500');
                }
            });
        }

        function validatePayload(payload) {
            var errors = {};

            if (!payload.title.trim()) {
                errors.title = ['Please enter a title.'];
            }

            if (!payload.span.trim()) {
                errors.span = ['Please select a display size.'];
            }

            return errors;
        }

        function initDragAndDrop() {
            if (!table) return;
            if (portfolioSortable) {
                portfolioSortable.destroy();
            }

            portfolioSortable = Sortable.create(table, {
                handle: '[data-admin-portfolios-drag-handle]',
                draggable: '[data-admin-portfolios-row]',
                animation: 180,
                ghostClass: 'opacity-25',
                chosenClass: 'bg-white/[0.055]',
                dragClass: 'shadow-2xl',
                delay: 120,
                delayOnTouchOnly: true,
                touchStartThreshold: 4,
                fallbackTolerance: 3,
                fallbackOnBody: true,
                onStart: function () {
                    shell.setAttribute('data-admin-portfolios-sorting', '');
                },
                onEnd: function (event) {
                    shell.removeAttribute('data-admin-portfolios-sorting');
                    if (event.oldIndex !== event.newIndex) {
                        saveNewOrder();
                    }
                },
            });
        }

        function saveNewOrder() {
            var ids = [];
            table.querySelectorAll('[data-portfolio-id]').forEach(function (row) {
                var id = parseInt(row.getAttribute('data-portfolio-id'), 10);
                if (id) {
                    ids.push(id);
                }
            });

            if (!ids.length) return;

            request(shell.dataset.reorderUrl, {
                method: 'PATCH',
                body: JSON.stringify({ ids: ids })
            }).then(function (response) {
                toast('success', 'Success', response.message || 'New order saved.');
                loadPortfolios();
            }).catch(function (error) {
                toast('danger', 'Unable to reorder', error.message || 'Please try again.');
            });
        }

        function render() {
            table.replaceChildren();

            var searchVal = (search?.value || '').toLowerCase().trim();
            var rows = portfolios;

            if (portfolioSortable) {
                // Reordering a service tab is safe; the backend preserves the
                // selected items' existing global sort slots.
                portfolioSortable.option('disabled', Boolean(searchVal));
            }

            if (searchVal) {
                rows = portfolios.filter(function (p) {
                    return (p.title || '').toLowerCase().indexOf(searchVal) > -1 ||
                        (p.video_url || '').toLowerCase().indexOf(searchVal) > -1;
                });
            }

            if (selectedServiceId !== '') {
                rows = rows.filter(function (p) {
                    return (p.service_id + '') === (selectedServiceId + '');
                });
            }

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');
                emptyCell.colSpan = 10;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No portfolio items found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (portfolio) {
                var row = document.createElement('tr');
                row.setAttribute('data-portfolio-id', portfolio.id);
                row.setAttribute('data-admin-portfolios-row', '');

                var dragCell = document.createElement('td');
                dragCell.className = 'px-3 py-2 w-16 text-center';
                dragCell.innerHTML = '<button type="button" class="inline-flex size-10 touch-none select-none items-center justify-center rounded-md border border-transparent text-white/35 transition hover:border-white/10 hover:bg-white/[0.06] hover:text-white/75 active:cursor-grabbing active:bg-white/10 cursor-grab" title="Drag to reorder" aria-label="Drag portfolio item to reorder" data-admin-portfolios-drag-handle>' +
                    '<svg class="size-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 6h.01M15 6h.01M9 12h.01M15 12h.01M9 18h.01M15 18h.01" />' +
                    '</svg>' +
                    '</button>';

                var imageCell = document.createElement('td');
                var imageWrap = document.createElement('div');
                var img = document.createElement('img');
                imageCell.className = 'px-5 py-4';
                imageWrap.className = 'relative aspect-video w-16 overflow-hidden border border-white/8 bg-black rounded';
                img.className = 'h-full w-full object-cover';
                img.src = portfolio.image || '/images/ai-director.jpg';
                img.alt = portfolio.title;
                img.draggable = false;
                imageWrap.appendChild(img);
                imageCell.appendChild(imageWrap);

                var titleCell = document.createElement('td');
                titleCell.className = 'px-5 py-4 font-medium text-white max-w-[200px] truncate';
                titleCell.textContent = portfolio.title;

                var serviceCell = document.createElement('td');
                serviceCell.className = 'px-5 py-4 text-white/60 text-xs max-w-[180px] truncate';
                serviceCell.textContent = portfolio.service_title || 'N/A';

                var videoCell = document.createElement('td');
                videoCell.className = 'px-5 py-4 text-white/48 font-mono text-xs max-w-[200px] truncate';
                videoCell.textContent = portfolio.video_url || 'N/A';

                var sizeCell = document.createElement('td');
                sizeCell.className = 'px-5 py-4 text-white/45 text-xs';
                var sizeText = '1/3 Width';
                if (portfolio.span === 'md:col-span-3') sizeText = '1/2 Width';
                else if (portfolio.span === 'md:col-span-4') sizeText = '2/3 Width';
                else if (portfolio.span === 'md:col-span-6') sizeText = 'Full Width';
                sizeCell.textContent = sizeText;

                var ratioCell = document.createElement('td');
                ratioCell.className = 'px-5 py-4 text-white/60 text-xs font-mono';
                ratioCell.textContent = portfolio.video_aspect_ratio || '16:9';

                var activeCell = document.createElement('td');
                activeCell.className = 'px-5 py-4';
                var activeBadge = document.createElement('span');
                if (portfolio.show_in_portfolio) {
                    activeBadge.className = 'rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400 border border-emerald-500/20';
                    activeBadge.textContent = 'Yes';
                } else {
                    activeBadge.className = 'rounded-full bg-white/5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white/38 border border-white/10';
                    activeBadge.textContent = 'No';
                }
                activeCell.appendChild(activeBadge);

                var orderCell = document.createElement('td');
                orderCell.className = 'px-5 py-4 text-white/35 font-mono text-xs';
                orderCell.textContent = portfolio.sort_order ?? 0;

                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.025]';
                actions.className = 'px-5 py-4';
                actionsWrap.className = 'flex justify-end gap-2';

                edit.type = 'button';
                edit.className = 'rounded border border-white/10 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-white/25 hover:text-white';
                edit.textContent = 'Edit';
                edit.addEventListener('click', function () {
                    openForm(portfolio);
                });

                remove.type = 'button';
                remove.className = 'rounded border border-[#e60012]/25 px-3 py-2 text-xs font-medium text-white/58 transition hover:border-[#e60012]/55 hover:bg-[#e60012]/12 hover:text-white';
                remove.textContent = 'Delete';
                remove.addEventListener('click', function () {
                    deletingId = portfolio.id;
                    deleteModal?.classList.remove('hidden');
                    deleteModal?.classList.add('grid');
                });

                actionsWrap.append(edit, remove);
                actions.appendChild(actionsWrap);
                row.append(dragCell, imageCell, titleCell, serviceCell, videoCell, sizeCell, ratioCell, activeCell, orderCell, actions);
                table.appendChild(row);
            });
        }

        function loadPortfolios() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            return request(url.toString(), { cache: 'no-store' }).then(function (payload) {
                portfolios = payload.portfolios || [];
                if (payload.services) {
                    services = payload.services;
                    renderServices();
                }
                render();
            }).catch(function (error) {
                toast('danger', 'Unable to load portfolio items', error.message || 'Please refresh the page and try again.');
            });
        }

        var uploadCard = shell.querySelector('[data-admin-portfolios-upload-card]');
        var fileInput = shell.querySelector('[data-admin-portfolios-file-input]');
        var previewWrapper = shell.querySelector('[data-admin-portfolios-preview-wrapper]');
        var previewImg = shell.querySelector('[data-admin-portfolios-preview]');
        var uploadPlaceholder = shell.querySelector('[data-admin-portfolios-upload-placeholder]');
        var removeImageBtn = shell.querySelector('[data-admin-portfolios-remove-image-file]');

        if (uploadCard && fileInput) {
            uploadCard.addEventListener('click', function () {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                var file = this.files[0];
                if (file) {
                    if (previewImg) previewImg.src = URL.createObjectURL(file);
                    if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                    if (previewWrapper) previewWrapper.classList.remove('hidden');
                    
                    var imgField = form.querySelector('[data-admin-portfolios-field="image"]');
                    if (imgField) imgField.value = '';

                    if (removeImageBtn) removeImageBtn.classList.remove('hidden');
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (fileInput) fileInput.value = '';
                if (previewWrapper) previewWrapper.classList.add('hidden');
                if (previewImg) previewImg.src = '';
                if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
                
                var imgField = form.querySelector('[data-admin-portfolios-field="image"]');
                if (imgField) imgField.value = '';
            });
        }

        shell.querySelector('[data-admin-portfolios-create]')?.addEventListener('click', function () {
            openForm(null);
        });

        shell.querySelector('[data-admin-portfolios-cancel]')?.addEventListener('click', resetForm);

        search?.addEventListener('input', function () {
            render();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            var id = form.querySelector('[data-admin-portfolios-id]').value;
            
            var payload = {
                title: form.elements.title.value.trim(),
                video_url: form.elements.video_url.value.trim(),
                video_aspect_ratio: form.elements.video_aspect_ratio.value,
                span: form.elements.span.value,
                show_in_portfolio: form.elements.show_in_portfolio.checked ? '1' : '0',
                sort_order: form.elements.sort_order.value,
            };

            var validationErrors = validatePayload(payload);
            if (Object.keys(validationErrors).length) {
                showErrors(validationErrors);
                toast('danger', 'Please check the form', 'Some details need attention.');
                return;
            }

            var url = id ? portfolioUrl(shell.dataset.updateUrlTemplate, id) : shell.dataset.storeUrl;
            
            var formData = new FormData(form);
            if (id) {
                formData.append('_method', 'PATCH');
            }
            formData.set('show_in_portfolio', payload.show_in_portfolio);

            setBusy(true);

            request(url, {
                method: 'POST',
                body: formData,
            }).then(function (response) {
                resetForm();
                toast('success', 'Success', response.message || 'Portfolio item saved.');
                return loadPortfolios();
            }).catch(function (error) {
                showErrors(error.errors || {});
                toast('danger', 'Unable to save', error.message || 'Please check the form.');
            }).finally(function () {
                setBusy(false);
            });
        });

        shell.querySelector('[data-admin-portfolios-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) {
                return;
            }

            deleteConfirm.disabled = true;

            request(portfolioUrl(shell.dataset.deleteUrlTemplate, deletingId), {
                method: 'DELETE',
            }).then(function (response) {
                toast('success', 'Success', response.message || 'Portfolio item deleted.');
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');
                return loadPortfolios();
            }).catch(function (error) {
                toast('danger', 'Unable to delete', error.message || 'Please try again.');
            }).finally(function () {
                deleteConfirm.disabled = false;
            });
        });

        function resetForm() {
            form.reset();
            form.querySelector('[data-admin-portfolios-id]').value = '';
            if (fileInput) fileInput.value = '';
            if (previewWrapper) previewWrapper.classList.add('hidden');
            if (previewImg) previewImg.src = '';
            if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
            clearErrors();
            formShell.classList.add('hidden');
        }

        function openForm(portfolio) {
            resetForm();
            formShell.classList.remove('hidden');

            shell.querySelector('[data-admin-portfolios-form-title]').textContent = portfolio ? 'Edit Portfolio Item' : 'Create Portfolio Item';

            if (portfolio) {
                form.querySelector('[data-admin-portfolios-id]').value = portfolio.id;
                form.elements.title.value = portfolio.title || '';
                form.elements.service_id.value = portfolio.service_id || '';
                form.elements.video_url.value = portfolio.video_url || '';
                form.elements.video_aspect_ratio.value = portfolio.video_aspect_ratio || '16:9';
                form.elements.span.value = portfolio.span || 'md:col-span-2';
                form.elements.show_in_portfolio.checked = Boolean(portfolio.show_in_portfolio);
                form.elements.sort_order.value = portfolio.sort_order ?? 0;
                
                var imgField = form.querySelector('[data-admin-portfolios-field="image"]');
                if (imgField) imgField.value = portfolio.image || '';

                if (portfolio.image) {
                    if (previewImg) previewImg.src = portfolio.image;
                    if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');
                    if (previewWrapper) previewWrapper.classList.remove('hidden');
                }
            } else {
                form.elements.service_id.value = selectedServiceId || '';
                form.elements.video_aspect_ratio.value = '16:9';
                form.elements.span.value = 'md:col-span-2';
                form.elements.show_in_portfolio.checked = true;
                form.elements.sort_order.value = (portfolios.length ? Math.max.apply(Math, portfolios.map(function(p){return p.sort_order;})) + 10 : 10);
            }
            form.elements.title.focus();
        }

        initDragAndDrop();
        render();
        loadPortfolios();
    });
}

function initAdminChrome() {
    if (adminShellReady) {
        return;
    }

    adminShellReady = true;

    document.addEventListener('click', function (event) {
        var openSidebar = event.target.closest('[data-admin-sidebar-open]');
        var closeSidebar = event.target.closest('[data-admin-sidebar-close], [data-admin-sidebar-link]');
        var userToggle = event.target.closest('[data-admin-user-menu-toggle]');

        if (openSidebar) {
            document.documentElement.setAttribute('data-admin-sidebar-open', '');
            return;
        }

        if (closeSidebar) {
            document.documentElement.removeAttribute('data-admin-sidebar-open');
        }

        if (userToggle) {
            var menu = userToggle.closest('[data-admin-user-menu]');
            var isOpen = menu.hasAttribute('data-open');

            document.querySelectorAll('[data-admin-user-menu][data-open]').forEach(function (item) {
                if (item !== menu) {
                    item.removeAttribute('data-open');
                    item.querySelector('[data-admin-user-menu-toggle]')?.setAttribute('aria-expanded', 'false');
                }
            });

            menu.toggleAttribute('data-open', !isOpen);
            userToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            return;
        }

        var adminModal = event.target.closest('[data-admin-modal]');

        if (adminModal && event.target === adminModal) {
            adminModal.querySelector('[data-admin-modal-cancel]')?.click();
            return;
        }

        document.querySelectorAll('[data-admin-user-menu][data-open]').forEach(function (menu) {
            if (!menu.contains(event.target)) {
                menu.removeAttribute('data-open');
                menu.querySelector('[data-admin-user-menu-toggle]')?.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        document.documentElement.removeAttribute('data-admin-sidebar-open');

        document.querySelectorAll('[data-admin-user-menu][data-open]').forEach(function (menu) {
            menu.removeAttribute('data-open');
            menu.querySelector('[data-admin-user-menu-toggle]')?.setAttribute('aria-expanded', 'false');
        });

        document.querySelector('[data-admin-modal] [data-admin-modal-cancel]')?.click();
    });
}

function finishNavigation() {
    document.documentElement.classList.remove('is-navigating');
    document.documentElement.removeAttribute('data-admin-sidebar-open');
    setMobileMenuOpen(false);

    if (window.location.hash) {
        return;
    }

    requestAnimationFrame(function () {
        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'auto',
        });
    });
}

document.addEventListener('DOMContentLoaded', initHomeAnimations);
document.addEventListener('DOMContentLoaded', initScrollTopButton);
document.addEventListener('DOMContentLoaded', initMarketingHeader);
document.addEventListener('DOMContentLoaded', initMobileMenu);
document.addEventListener('DOMContentLoaded', initAdminHomeForm);
document.addEventListener('DOMContentLoaded', initToastEvents);
document.addEventListener('DOMContentLoaded', initContactForm);
document.addEventListener('DOMContentLoaded', initDirectorTabs);
document.addEventListener('DOMContentLoaded', initAdminUsers);
document.addEventListener('DOMContentLoaded', initSortableAdminPages);
document.addEventListener('DOMContentLoaded', initAdminChrome);
window.addEventListener('scroll', syncScrollTopButton, { passive: true });
window.addEventListener('scroll', syncMarketingHeader, { passive: true });

document.addEventListener('livewire:init', function () {
    Livewire.hook('request', function (request) {
        request.succeed(function () {
            requestAnimationFrame(initHomeAnimations);
        });
    });
});

document.addEventListener('livewire:navigating', function () {
    document.documentElement.classList.add('is-navigating');
});

document.addEventListener('livewire:navigated', initHomeAnimations);
document.addEventListener('livewire:navigated', initScrollTopButton);
document.addEventListener('livewire:navigated', initMarketingHeader);
document.addEventListener('livewire:navigated', initMobileMenu);
document.addEventListener('livewire:navigated', initAdminHomeForm);
document.addEventListener('livewire:navigated', initToastEvents);
document.addEventListener('livewire:navigated', initContactForm);
document.addEventListener('livewire:navigated', initDirectorTabs);
document.addEventListener('livewire:navigated', initAdminUsers);
document.addEventListener('livewire:navigated', initSortableAdminPages);
document.addEventListener('livewire:navigated', initAdminChrome);
document.addEventListener('livewire:navigated', finishNavigation);
