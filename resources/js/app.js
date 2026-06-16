var revealObserver;
var scrollTopButton;
var mobileMenuShell;
var toastRoot;
var toastEventsReady = false;
var toastLimit = 5;
var contactServiceOutsideReady = false;
var adminShellReady = false;

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

function initScrollTopButton() {
    if (scrollTopButton) {
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
            submitLabel.textContent = 'Sending...';

            window.setTimeout(function () {
                form.reset();
                submit.disabled = false;
                submitLabel.textContent = 'Send Message';

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

                showToast('success', 'Message received.', "Thank you for reaching out. We'll be in touch within 24 hours.");
            }, 450);
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

        function playVideo(url, title) {
            if (!videoModal) return;

            if (videoTitle) {
                videoTitle.textContent = title || '';
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

            if (videoTrigger) {
                var url = videoTrigger.getAttribute('data-video-url');
                var title = videoTrigger.getAttribute('data-video-title');
                playVideo(url, title);
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

            return request(url.toString()).then(function (payload) {
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

        if (!table || !form) {
            return;
        }

        shell.setAttribute('data-admin-directors-ready', '');

        var worksContainer = shell.querySelector('[data-admin-directors-works-container]');
        var addWorkButton = shell.querySelector('[data-admin-directors-add-work]');
        var worksTemplate = shell.querySelector('[data-admin-directors-work-row-template]');
        var worksRawInput = shell.querySelector('[data-admin-directors-works-raw-input]');

        function renumberWorks() {
            if (!worksContainer) return;
            worksContainer.querySelectorAll('[data-admin-directors-work-row]').forEach(function (row, index) {
                var numberIndicator = row.querySelector('[data-admin-directors-work-number]');
                if (numberIndicator) {
                    numberIndicator.textContent = '#' + (index + 1);
                }
            });
        }

        function addWorkRow(data) {
            if (!worksContainer || !worksTemplate) return;
            var clone = worksTemplate.content.cloneNode(true);
            var row = clone.querySelector('[data-admin-directors-work-row]');

            if (data) {
                var titleField = row.querySelector('[data-admin-directors-work-field="title"]');
                var videoUrlField = row.querySelector('[data-admin-directors-work-field="video_url"]');
                var imageField = row.querySelector('[data-admin-directors-work-field="image"]');
                var spanField = row.querySelector('[data-admin-directors-work-field="span"]');

                if (titleField) titleField.value = data.title || '';
                if (videoUrlField) videoUrlField.value = data.video_url || '';
                if (imageField) imageField.value = data.image || '';
                if (spanField) spanField.value = data.span || 'md:col-span-2';
            }

            var videoUrlField = row.querySelector('[data-admin-directors-work-field="video_url"]');
            var imageField = row.querySelector('[data-admin-directors-work-field="image"]');
            if (videoUrlField && imageField) {
                videoUrlField.addEventListener('input', function () {
                    imageField.value = '';
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
            worksContainer.querySelectorAll('[data-admin-directors-work-row]').forEach(function (row) {
                var title = row.querySelector('[data-admin-directors-work-field="title"]')?.value.trim() || '';
                var videoUrl = row.querySelector('[data-admin-directors-work-field="video_url"]')?.value.trim() || '';
                var image = row.querySelector('[data-admin-directors-work-field="image"]')?.value.trim() || '';
                var span = row.querySelector('[data-admin-directors-work-field="span"]')?.value || 'md:col-span-2';

                if (title || videoUrl) {
                    works.push({
                        title: title,
                        video_url: videoUrl,
                        image: image,
                        span: span
                    });
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
            });
        }

        function validateDirectorPayload(payload, hasFile) {
            var errors = {};

            if (!payload.first_name.trim()) errors.first_name = ['Please enter a first name.'];
            if (!payload.last_name.trim()) errors.last_name = ['Please enter a last name.'];
            if (!payload.slug.trim()) {
                errors.slug = ['Please enter a slug.'];
            } else if (!/^[a-z0-9-_]+$/.test(payload.slug.trim())) {
                errors.slug = ['The slug must only contain lowercase letters, numbers, dashes, and underscores.'];
            }
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

            if (!payload.works_raw.trim()) {
                errors.works_raw = ['Please enter works configuration.'];
            } else {
                try {
                    var parsed = JSON.parse(payload.works_raw);
                    if (!Array.isArray(parsed)) {
                        errors.works_raw = ['The works must be a JSON array.'];
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
            form.querySelector('[data-admin-directors-id]').value = '';
            shell.querySelector('[data-admin-directors-form-title]').textContent = 'Create Director';
            if (worksContainer) {
                worksContainer.replaceChildren();
            }
            formShell.classList.add('hidden');
        }

        function openForm(director) {
            clearErrors();
            formShell.classList.remove('hidden');
            form.querySelector('[data-admin-directors-id]').value = director?.id || '';
            
            form.elements.first_name.value = director?.first_name || '';
            form.elements.last_name.value = director?.last_name || '';
            form.elements.slug.value = director?.slug || '';
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

            shell.querySelector('[data-admin-directors-form-title]').textContent = director ? 'Edit Director' : 'Create Director';
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
                actionsWrap.append(edit, remove);
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

            return request(url.toString()).then(function (payload) {
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

        shell.querySelector('[data-admin-directors-delete-cancel]')?.addEventListener('click', function () {
            deletingId = null;
            deleteModal?.classList.add('hidden');
            deleteModal?.classList.remove('grid');
        });

        deleteConfirm?.addEventListener('click', function () {
            if (!deletingId) {
                return;
            }

            deleteConfirm.disabled = true;

            request(directorUrl(shell.dataset.deleteUrlTemplate, deletingId), {
                method: 'DELETE',
            }).then(function (response) {
                toast('success', 'Success', response.message || 'Director deleted.');
                deletingId = null;
                deleteModal?.classList.add('hidden');
                deleteModal?.classList.remove('grid');
                return loadDirectors();
            }).catch(function (error) {
                toast('danger', 'Unable to delete', error.message || 'Please try again.');
            }).finally(function () {
                deleteConfirm.disabled = false;
            });
        });

        render();
    });
}

function initAdminFaqs() {
    document.querySelectorAll('[data-admin-faqs]:not([data-admin-faqs-ready])').forEach(function (shell) {
        var initialData = shell.querySelector('[data-admin-faqs-initial]');
        var table = shell.querySelector('[data-admin-faqs-table]');
        var formShell = shell.querySelector('[data-admin-faqs-form-shell]');
        var form = shell.querySelector('[data-admin-faqs-form]');
        var search = shell.querySelector('[data-admin-faqs-search]');
        var deleteModal = shell.querySelector('[data-admin-faqs-delete-modal]');
        var deleteConfirm = shell.querySelector('[data-admin-faqs-delete-confirm]');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var faqs = [];
        var deletingId = null;
        var searchTimer;

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
            form.elements.sort_order.value = faq ? faq.sort_order : '10';
            shell.querySelector('[data-admin-faqs-form-title]').textContent = faq ? 'Edit FAQ' : 'Create FAQ';
            form.elements.question.focus();
        }

        function filteredFaqs() {
            var value = (search?.value || '').trim().toLowerCase();

            if (!value) {
                return faqs;
            }

            return faqs.filter(function (faq) {
                return (
                    faq.category.toLowerCase().includes(value) ||
                    faq.question.toLowerCase().includes(value) ||
                    faq.answer.toLowerCase().includes(value) ||
                    (faq.keywords && faq.keywords.toLowerCase().includes(value))
                );
            });
        }

        function render() {
            var rows = filteredFaqs();

            table.replaceChildren();

            if (!rows.length) {
                var emptyRow = document.createElement('tr');
                var emptyCell = document.createElement('td');

                emptyCell.colSpan = 5;
                emptyCell.className = 'px-5 py-12 text-center text-sm text-white/35';
                emptyCell.textContent = 'No FAQs found.';
                emptyRow.appendChild(emptyCell);
                table.appendChild(emptyRow);
                return;
            }

            rows.forEach(function (faq) {
                var row = document.createElement('tr');
                var categoryCell = document.createElement('td');
                var questionCell = document.createElement('td');
                var answerCell = document.createElement('td');
                var orderCell = document.createElement('td');
                var actions = document.createElement('td');
                var actionsWrap = document.createElement('div');
                var edit = document.createElement('button');
                var remove = document.createElement('button');

                row.className = 'transition hover:bg-white/[0.035]';
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
                row.append(categoryCell, questionCell, answerCell, orderCell, actions);
                table.appendChild(row);
            });
        }

        function loadFaqs() {
            var url = new URL(shell.dataset.indexUrl, window.location.origin);

            if (search?.value.trim()) {
                url.searchParams.set('search', search.value.trim());
            }

            return request(url.toString()).then(function (payload) {
                faqs = payload.faqs || [];
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
            clearTimeout(searchTimer);
            searchTimer = window.setTimeout(loadFaqs, 300);
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

        render();
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
document.addEventListener('DOMContentLoaded', initMobileMenu);
document.addEventListener('DOMContentLoaded', initToastEvents);
document.addEventListener('DOMContentLoaded', initContactForm);
document.addEventListener('DOMContentLoaded', initDirectorTabs);
document.addEventListener('DOMContentLoaded', initAdminUsers);
document.addEventListener('DOMContentLoaded', initAdminDirectors);
document.addEventListener('DOMContentLoaded', initAdminFaqs);
document.addEventListener('DOMContentLoaded', initAdminChrome);
window.addEventListener('scroll', syncScrollTopButton, { passive: true });

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
document.addEventListener('livewire:navigated', initMobileMenu);
document.addEventListener('livewire:navigated', initToastEvents);
document.addEventListener('livewire:navigated', initContactForm);
document.addEventListener('livewire:navigated', initDirectorTabs);
document.addEventListener('livewire:navigated', initAdminUsers);
document.addEventListener('livewire:navigated', initAdminDirectors);
document.addEventListener('livewire:navigated', initAdminFaqs);
document.addEventListener('livewire:navigated', initAdminChrome);
document.addEventListener('livewire:navigated', finishNavigation);
