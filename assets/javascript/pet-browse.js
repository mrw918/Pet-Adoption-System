(function () {
    'use strict';

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str == null ? '' : String(str);
        return d.innerHTML;
    }

    function escapeAttr(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function parseAgeYears(ageStr) {
        const m = String(ageStr).match(/(\d+)/);
        return m ? parseInt(m[1], 10) : 0;
    }

    /** Bucket id for fixed age-range filter. */
    function getPetAgeBucket(pet) {
        const raw = String(pet.age || '');
        const n = parseAgeYears(raw);
        if (n === 0 && !/\d/.test(raw)) {
            return 'unknown';
        }
        if (n <= 2) {
            return '0-2';
        }
        if (n <= 5) {
            return '3-5';
        }
        if (n <= 8) {
            return '6-8';
        }
        return '9+';
    }

    function splitTraits(personality) {
        return String(personality || '')
            .split(/[,，]/u)
            .map(function (t) {
                return t.trim();
            })
            .filter(Boolean);
    }

    const el = document.getElementById('pets-json');
    let petsData = [];
    try {
        petsData = el ? JSON.parse(el.textContent || '[]') : [];
    } catch (e) {
        petsData = [];
    }

    const cfgEl = document.getElementById('browse-config');
    let browseCsrf = '';
    let verifyEmail = '';
    let verifyPhone = '';
    try {
        const cfg = cfgEl ? JSON.parse(cfgEl.textContent || '{}') : {};
        browseCsrf = typeof cfg.csrf === 'string' ? cfg.csrf : '';
        verifyEmail = typeof cfg.userEmail === 'string' ? cfg.userEmail.trim() : '';
        verifyPhone = typeof cfg.userPhone === 'string' ? cfg.userPhone.trim() : '';
    } catch (e2) {
        browseCsrf = '';
        verifyEmail = '';
        verifyPhone = '';
    }

    const activeFilters = {
        breeds: [],
        ageRanges: [],
        personalities: [],
        status: 'all',
    };

    let currentPet = null;

    function getUniqueOptions() {
        const breeds = Array.from(new Set(petsData.map(function (p) {
            return p.breed;
        }))).filter(Boolean).sort();
        const traits = new Set();
        petsData.forEach(function (p) {
            splitTraits(p.personality).forEach(function (t) {
                traits.add(t);
            });
        });
        return {
            breeds: breeds,
            personalities: Array.from(traits).sort(),
        };
    }

    function createFilterButtons() {
        const opts = getUniqueOptions();
        const breedEl = document.getElementById('breedFilters');
        const persEl = document.getElementById('personalityFilters');
        if (!breedEl || !persEl) return;

        breedEl.innerHTML = opts.breeds.map(function (b) {
            return '<button type="button" class="browse-chip" data-filter="breeds" data-value="' +
                escapeAttr(b) + '">' + escapeHtml(b) + '</button>';
        }).join('');

        persEl.innerHTML = opts.personalities.map(function (p) {
            return '<button type="button" class="browse-chip" data-filter="personalities" data-value="' +
                escapeAttr(p) + '">' + escapeHtml(p) + '</button>';
        }).join('');

        breedEl.querySelectorAll('[data-filter]').forEach(bindToggle);
        persEl.querySelectorAll('[data-filter]').forEach(bindToggle);

        const ageEl = document.getElementById('ageFilters');
        if (ageEl) {
            ageEl.querySelectorAll('[data-filter="ageRanges"]').forEach(bindToggle);
        }
    }

    function bindToggle(btn) {
        btn.addEventListener('click', function () {
            const type = btn.getAttribute('data-filter');
            const value = btn.getAttribute('data-value');
            if (!type || value == null) return;
            const arr = activeFilters[type];
            if (!Array.isArray(arr)) return;
            const i = arr.indexOf(value);
            if (i >= 0) {
                arr.splice(i, 1);
                btn.classList.remove('is-active');
            } else {
                arr.push(value);
                btn.classList.add('is-active');
            }
            applyFilters();
        });
    }

    function syncStatusUI() {
        const st = activeFilters.status;
        document.querySelectorAll('[data-status-tile]').forEach(function (b) {
            const key = b.getAttribute('data-status-tile');
            const on = (key === 'available' && st === 'available') || (key === 'adopted' && st === 'adopted');
            b.classList.toggle('is-selected', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        const showAll = document.getElementById('statusShowAll');
        if (showAll) {
            const allOn = st === 'all';
            showAll.setAttribute('aria-pressed', allOn ? 'true' : 'false');
            showAll.classList.toggle('is-active', allOn);
        }
    }

    document.querySelectorAll('[data-status-tile]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activeFilters.status = btn.getAttribute('data-status-tile') || 'all';
            syncStatusUI();
            applyFilters();
        });
    });

    const statusShowAll = document.getElementById('statusShowAll');
    if (statusShowAll) {
        statusShowAll.addEventListener('click', function () {
            activeFilters.status = 'all';
            syncStatusUI();
            applyFilters();
        });
    }

    function applyFilters() {
        const keyword = (document.getElementById('searchInput').value || '').toLowerCase().trim();

        let filtered = petsData.filter(function (pet) {
            const hay = (pet.name + ' ' + pet.breed + ' ' + pet.personality).toLowerCase();
            const matchSearch = !keyword || hay.indexOf(keyword) !== -1;

            const matchBreed = activeFilters.breeds.length === 0 ||
                activeFilters.breeds.indexOf(pet.breed) !== -1;

            const bucket = getPetAgeBucket(pet);
            const matchAge = activeFilters.ageRanges.length === 0 ||
                activeFilters.ageRanges.indexOf(bucket) !== -1;

            const matchPersonality = activeFilters.personalities.length === 0 ||
                activeFilters.personalities.some(function (t) {
                    return String(pet.personality || '').indexOf(t) !== -1;
                });

            let matchStatus = true;
            if (activeFilters.status === 'available') {
                matchStatus = pet.statusKey === 'available';
            } else if (activeFilters.status === 'adopted') {
                matchStatus = pet.statusKey === 'adopted';
            }

            return matchSearch && matchBreed && matchAge && matchPersonality && matchStatus;
        });

        const sortMode = document.getElementById('sortSelect').value;
        if (sortMode === 'ageAsc') {
            filtered.sort(function (a, b) {
                return parseAgeYears(a.age) - parseAgeYears(b.age);
            });
        } else if (sortMode === 'ageDesc') {
            filtered.sort(function (a, b) {
                return parseAgeYears(b.age) - parseAgeYears(a.age);
            });
        }

        renderPetCards(filtered);
    }

    function badgeClass(key) {
        if (key === 'available') return 'available';
        if (key === 'adopted') return 'adopted';
        return 'other';
    }

    function renderPetCards(list) {
        const grid = document.getElementById('petGrid');
        const empty = document.getElementById('emptyState');
        const totalEl = document.getElementById('totalCount');
        const infoEl = document.getElementById('resultInfo');
        if (!grid) return;

        grid.innerHTML = '';
        list.forEach(function (pet) {
            const card = document.createElement('article');
            card.className = 'browse-card';
            card.setAttribute('data-id', String(pet.id));
            const badgeKey = pet.statusKey || 'other';
            const btnLabel = pet.statusKey === 'available'
                ? 'Request adoption'
                : 'View profile';

            card.innerHTML =
                '<div class="browse-card-img-wrap">' +
                '<img class="browse-card-img" src="' + escapeHtml(pet.imageUrl) + '" alt="' +
                escapeHtml(pet.name) + '">' +
                '<span class="browse-card-badge ' + escapeHtml(badgeClass(badgeKey)) + '">' +
                escapeHtml(pet.status || '') + '</span>' +
                '<span class="browse-card-tag">Adopt</span>' +
                '</div>' +
                '<div class="browse-card-body">' +
                '<div class="browse-card-title-row">' +
                '<div><h3 class="browse-card-name">' + escapeHtml(pet.name) + '</h3>' +
                '<p class="browse-card-breed">' + escapeHtml(pet.breed) + '</p></div>' +
                '<span class="browse-card-gender" aria-hidden="true">' + escapeHtml(pet.gender) + '</span>' +
                '</div>' +
                '<div class="browse-card-meta">Age: ' + escapeHtml(pet.age) + '<br>Health: ' +
                escapeHtml(pet.health) + '</div>' +
                '<button type="button" class="browse-card-btn' +
                (pet.statusKey === 'adopted' ? ' secondary' : '') + '" data-action="apply">' +
                escapeHtml(btnLabel) + '</button>' +
                '</div>';
            grid.appendChild(card);
        });

        if (totalEl) totalEl.textContent = String(list.length);
        if (infoEl) {
            infoEl.textContent = list.length
                ? list.length + ' match your filters'
                : '';
        }
        if (empty) {
            empty.hidden = list.length > 0;
        }
    }

    function findPet(id) {
        const n = Number(id);
        for (let i = 0; i < petsData.length; i++) {
            if (petsData[i].id === n) return petsData[i];
        }
        return null;
    }

    function showModal(pet) {
        if (!pet) return;
        currentPet = pet;
        const modal = document.getElementById('petModal');
        const flash = document.getElementById('applyFlash');
        if (flash) flash.remove();

        document.getElementById('modalImage').src = pet.imageUrl || '';
        document.getElementById('modalImage').alt = pet.name || '';
        document.getElementById('modalName').textContent = pet.name || '';
        document.getElementById('modalBreed').textContent = pet.breed || '';
        document.getElementById('modalGender').textContent = pet.gender || '';
        document.getElementById('modalAge').textContent = pet.age || '';
        document.getElementById('modalHealth').textContent = pet.health || '';
        document.getElementById('modalPersonality').textContent = pet.personality || '';
        const verifyWrap = document.getElementById('modalVerify');
        const verifyEmailEl = document.getElementById('modalVerifyEmail');
        const verifyPhoneEl = document.getElementById('modalVerifyPhone');
        if (verifyWrap && verifyEmailEl && verifyPhoneEl) {
            verifyWrap.hidden = false;
            verifyEmailEl.textContent = verifyEmail || 'Not provided';
            verifyPhoneEl.textContent = verifyPhone || 'Not provided';
        }

        const note = document.getElementById('modalApplyNote');
        const btn = document.getElementById('modalApplyBtn');
        if (note && btn) {
            if (pet.statusKey === 'available') {
                note.textContent = 'Submit a request below. Staff will see your application in the admin console and follow up.';
                btn.disabled = false;
                btn.textContent = 'Request adoption';
            } else {
                note.textContent = 'This pet is no longer listed as available. You can still read their profile.';
                btn.disabled = true;
                btn.textContent = 'Not available for new requests';
            }
        }

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        const modal = document.getElementById('petModal');
        if (modal) modal.hidden = true;
        document.body.style.overflow = '';
        currentPet = null;
    }

    function showApplyFlash(message, isError) {
        const body = document.querySelector('.browse-modal-body');
        if (!body) return;
        const old = document.getElementById('applyFlash');
        if (old) old.remove();
        const flash = document.createElement('div');
        flash.id = 'applyFlash';
        flash.className = 'browse-flash' + (isError ? ' browse-flash--err' : '');
        flash.textContent = message;
        const cta = document.getElementById('modalApplyBtn');
        if (cta && cta.nextSibling) {
            body.insertBefore(flash, cta.nextSibling);
        } else {
            body.appendChild(flash);
        }
    }

    const modalClose = document.getElementById('modalClose');
    if (modalClose) modalClose.addEventListener('click', hideModal);
    document.querySelectorAll('[data-close-modal]').forEach(function (n) {
        n.addEventListener('click', hideModal);
    });

    const modalApplyBtn = document.getElementById('modalApplyBtn');
    if (modalApplyBtn) {
        modalApplyBtn.addEventListener('click', function () {
            if (!currentPet || currentPet.statusKey !== 'available') return;
            if (!browseCsrf) {
                showApplyFlash('Could not verify this page. Please refresh and try again.', true);
                return;
            }
            const btn = modalApplyBtn;
            const petId = currentPet.id;
            btn.disabled = true;
            fetch('apply_pet.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pet_id: petId, csrf: browseCsrf }),
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { status: res.status, data: data };
                    });
                })
                .then(function (out) {
                    const d = out.data || {};
                    if (out.status >= 200 && out.status < 300 && d.ok) {
                        showApplyFlash(d.message || 'Your application has been submitted.', false);
                    } else {
                        showApplyFlash(d.error || 'Something went wrong. Please try again.', true);
                        btn.disabled = false;
                    }
                })
                .catch(function () {
                    showApplyFlash('Network error. Please try again.', true);
                    btn.disabled = false;
                });
        });
    }

    const resetBtn = document.getElementById('resetFilters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            activeFilters.breeds = [];
            activeFilters.ageRanges = [];
            activeFilters.personalities = [];
            activeFilters.status = 'all';
            document.querySelectorAll('#breedFilters .browse-chip, #ageFilters .browse-chip, #personalityFilters .browse-chip')
                .forEach(function (c) {
                    c.classList.remove('is-active');
                });
            syncStatusUI();
            const search = document.getElementById('searchInput');
            const sort = document.getElementById('sortSelect');
            if (search) search.value = '';
            if (sort) sort.value = 'default';
            applyFilters();
        });
    }

    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hideModal();
    });

    createFilterButtons();
    syncStatusUI();
    applyFilters();

    const petGrid = document.getElementById('petGrid');
    if (petGrid) {
        petGrid.addEventListener('click', function (e) {
            const card = e.target.closest('.browse-card');
            if (!card) return;
            const id = card.getAttribute('data-id');
            if (e.target.closest('[data-action="apply"]')) {
                e.stopPropagation();
                const pet = findPet(id);
                if (pet) {
                    showModal(pet);
                }
                return;
            }
            const pet = findPet(id);
            if (pet) showModal(pet);
        });
    }
})();
