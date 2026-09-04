{{-- Global Reusable Published Fatwa Mention / Cross-Referencing Modal (Multi-Select & Guaranteed Insertion) --}}
<div class="modal fade" id="fatwaMentionModal" tabindex="-1" aria-labelledby="fatwaMentionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            {{-- Header --}}
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success px-2 py-1 fs-6">@</span>
                    <h5 class="modal-title fw-bold mb-0" id="fatwaMentionModalLabel">Select Published Fatwa(s) to Reference</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Search & Selection Bar --}}
            <div class="p-3 bg-light border-bottom">
                <div class="input-group input-group-lg shadow-sm mb-2">
                    <span class="input-group-text bg-white border-end-0 text-success">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="fatwaMentionSearchInput" class="form-control border-start-0 ps-0"
                           placeholder="Type Question ID (e.g. A0001) or title keyword..." autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button" id="fatwaMentionClearSearch">✕</button>
                </div>
                <div class="d-flex justify-content-between align-items-center px-1">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" id="fatwaSelectAllCb">
                            <label class="form-check-label small fw-semibold text-muted" for="fatwaSelectAllCb">Select All</label>
                        </div>
                        <small class="text-muted" id="fatwaMentionCountText">Showing published fatwas</small>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1" id="selectedPillBadge">
                            <span id="selectedCount">0</span> selected
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body List --}}
            <div class="modal-body p-0" style="max-height: 420px; overflow-y: auto;">
                <div id="fatwaMentionLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted small mt-2">Searching published fatwas...</div>
                </div>

                <div class="list-group list-group-flush" id="fatwaMentionList">
                    {{-- Dynamically populated --}}
                </div>

                <div id="fatwaMentionEmpty" class="text-center py-5 d-none">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">No published fatwas found matching your query.</p>
                </div>
            </div>

            {{-- Footer with Multi-Select Action --}}
            <div class="modal-footer py-2 px-4 bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>Check multiple fatwas or click <strong>Quick Insert</strong> on any row.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3 rounded-pill border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm px-4 rounded-pill fw-bold shadow-sm" id="insertSelectedFatwasBtn" disabled>
                        <i class="bi bi-tags-fill me-1"></i> Insert Selected (<span id="btnSelectedCount">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fatwa-mention-row {
    transition: all 0.15s ease-in-out;
    cursor: pointer;
}
.fatwa-mention-row:hover {
    background-color: #f0fdf4 !important;
}
.fatwa-mention-row.selected-row {
    background-color: #ecfdf5 !important;
    border-left: 4px solid #16a34a !important;
}
.fatwa-mention-row .badge-id {
    background-color: #1a7a4a;
    color: #fff;
    font-weight: 700;
    font-size: 0.88rem;
    padding: 4px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
}
.fatwa-mention-cb {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let activeEditorTarget = null;
    let searchDebounceTimer = null;
    let selectedFatwaIds = new Set();
    let currentLoadedFatwas = [];

    const modalEl = document.getElementById('fatwaMentionModal');
    const modal = new bootstrap.Modal(modalEl);
    const searchInput = document.getElementById('fatwaMentionSearchInput');
    const listContainer = document.getElementById('fatwaMentionList');
    const loadingEl = document.getElementById('fatwaMentionLoading');
    const emptyEl = document.getElementById('fatwaMentionEmpty');
    const countEl = document.getElementById('fatwaMentionCountText');
    const clearBtn = document.getElementById('fatwaMentionClearSearch');
    const selectAllCb = document.getElementById('fatwaSelectAllCb');
    const insertSelectedBtn = document.getElementById('insertSelectedFatwasBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const btnSelectedCountSpan = document.getElementById('btnSelectedCount');

    function getSummernoteInstance() {
        if (activeEditorTarget) {
            const $t = $(activeEditorTarget);
            if ($t.data('summernote') !== undefined) {
                return $t;
            }
            if ($t.hasClass('note-editable')) {
                return $t.closest('.note-editor').prev('textarea');
            }
        }
        // Check textarea[name="full_answer"]
        const $fa = $('textarea[name="full_answer"]');
        if ($fa.length && $fa.data('summernote') !== undefined) {
            return $fa;
        }
        // Check #summernote
        const $sn = $('#summernote');
        if ($sn.length && $sn.data('summernote') !== undefined) {
            return $sn;
        }
        // Check #answer_editor
        const $ae = $('#answer_editor');
        if ($ae.length && $ae.data('summernote') !== undefined) {
            return $ae;
        }
        // Check any initialized summernote
        const $any = $('textarea, div').filter(function () {
            return $(this).data('summernote') !== undefined;
        });
        if ($any.length) {
            return $any.first();
        }
        return null;
    }

    // Global function to trigger modal for a specific textarea or summernote
    window.openFatwaMentionPicker = function (targetEl, initialQuery = '', isSummernote = false) {
        activeEditorTarget = targetEl || document.querySelector('textarea[name=full_answer], #summernote, #answer_editor');
        
        const $sn = getSummernoteInstance();
        if ($sn && $sn.length && $sn.data('summernote') !== undefined) {
            try {
                $sn.summernote('saveRange');
            } catch (e) {}
        }

        // Reset selections on modal open
        selectedFatwaIds.clear();
        updateSelectionUI();
        if (selectAllCb) selectAllCb.checked = false;

        searchInput.value = initialQuery;
        modal.show();
        loadFatwas(initialQuery);
    };

    modalEl.addEventListener('shown.bs.modal', function () {
        searchInput.focus();
        searchInput.select();
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.focus();
        loadFatwas('');
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            loadFatwas(searchInput.value.trim());
        }, 250);
    });

    if (selectAllCb) {
        selectAllCb.addEventListener('change', function () {
            const isChecked = this.checked;
            currentLoadedFatwas.forEach(f => {
                if (isChecked) {
                    selectedFatwaIds.add(f.question_id);
                } else {
                    selectedFatwaIds.delete(f.question_id);
                }
            });
            document.querySelectorAll('.fatwa-mention-cb').forEach(cb => {
                cb.checked = isChecked;
                const row = cb.closest('.fatwa-mention-row');
                if (row) {
                    if (isChecked) row.classList.add('selected-row');
                    else row.classList.remove('selected-row');
                }
            });
            updateSelectionUI();
        });
    }

    insertSelectedBtn.addEventListener('click', function () {
        if (selectedFatwaIds.size === 0) return;
        
        const tags = Array.from(selectedFatwaIds).map(id => '@{' + id + '}').join(' ');
        insertTagsIntoAnswer(tags);
        modal.hide();
    });

    function updateSelectionUI() {
        const count = selectedFatwaIds.size;
        selectedCountSpan.textContent = count;
        btnSelectedCountSpan.textContent = count;
        insertSelectedBtn.disabled = count === 0;
    }

    function loadFatwas(query) {
        loadingEl.classList.remove('d-none');
        emptyEl.classList.add('d-none');
        listContainer.innerHTML = '';

        fetch('{{ route("fatwa.mention.search") }}?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                loadingEl.classList.add('d-none');
                currentLoadedFatwas = data || [];

                if (!data || data.length === 0) {
                    emptyEl.classList.remove('d-none');
                    countEl.textContent = '0 results';
                    return;
                }

                countEl.textContent = data.length + ' published fatwa(s) available';

                data.forEach(item => {
                    const isSelected = selectedFatwaIds.has(item.question_id);
                    const row = document.createElement('div');
                    row.className = 'list-group-item fatwa-mention-row p-3 border-start-0 border-end-0 d-flex justify-content-between align-items-center gap-3 ' + (isSelected ? 'selected-row' : '');
                    
                    row.innerHTML = `
                        <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden">
                            <input type="checkbox" class="form-check-input fatwa-mention-cb flex-shrink-0" value="${escapeHtml(item.question_id)}" ${isSelected ? 'checked' : ''}>
                            <span class="badge-id flex-shrink-0">${escapeHtml(item.question_id)}</span>
                            <div class="text-truncate">
                                <h6 class="mb-1 text-dark fw-bold text-truncate" title="${escapeHtml(item.title)}">${escapeHtml(item.title)}</h6>
                                <div class="text-muted small d-flex align-items-center gap-2">
                                    <span><i class="bi bi-calendar3 me-1"></i>${escapeHtml(item.published_date)}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success flex-shrink-0 rounded-pill px-3 fw-semibold quick-insert-btn" title="Quick Insert this reference only">
                            <i class="bi bi-tag-fill me-1"></i>Insert @{${escapeHtml(item.question_id)}}
                        </button>
                    `;

                    // Checkbox toggle
                    const cb = row.querySelector('.fatwa-mention-cb');
                    cb.addEventListener('click', function(e) {
                        e.stopPropagation();
                        toggleItem(item.question_id, cb.checked, row);
                    });

                    // Row click toggles checkbox
                    row.addEventListener('click', function(e) {
                        if (e.target.closest('.quick-insert-btn')) return;
                        cb.checked = !cb.checked;
                        toggleItem(item.question_id, cb.checked, row);
                    });

                    // Quick 1-click single insert
                    const quickBtn = row.querySelector('.quick-insert-btn');
                    quickBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        insertTagsIntoAnswer('@{' + item.question_id + '}');
                        modal.hide();
                    });

                    listContainer.appendChild(row);
                });
            })
            .catch(err => {
                loadingEl.classList.add('d-none');
                emptyEl.classList.remove('d-none');
                console.error('Error fetching fatwa mentions:', err);
            });
    }

    function toggleItem(id, isChecked, row) {
        if (isChecked) {
            selectedFatwaIds.add(id);
            row.classList.add('selected-row');
        } else {
            selectedFatwaIds.delete(id);
            row.classList.remove('selected-row');
        }
        updateSelectionUI();
    }

    function insertTagsIntoAnswer(tagsString) {
        if (!tagsString) return;

        const $sn = getSummernoteInstance();

        if ($sn && $sn.length && $sn.data('summernote') !== undefined) {
            $sn.summernote('focus');
            try {
                $sn.summernote('restoreRange');
            } catch (e) {}

            try {
                $sn.summernote('pasteHTML', ' ' + tagsString + ' ');
            } catch (e) {
                console.warn('pasteHTML error, using insertText fallback:', e);
                try {
                    $sn.summernote('insertText', ' ' + tagsString + ' ');
                } catch (err2) {
                    const currentHtml = $sn.summernote('code');
                    $sn.summernote('code', currentHtml + ' ' + tagsString + ' ');
                }
            }

            // Sync hidden textarea value
            $sn.val($sn.summernote('code'));
            $sn.trigger('change');
            return;
        }

        // Standard Textarea Fallback
        let targetTextarea = activeEditorTarget;
        if (!targetTextarea || targetTextarea.tagName !== 'TEXTAREA') {
            targetTextarea = document.querySelector('textarea[name=full_answer], #answer_editor, #summernote, textarea');
        }

        if (targetTextarea) {
            const start = targetTextarea.selectionStart !== undefined ? targetTextarea.selectionStart : targetTextarea.value.length;
            const end = targetTextarea.selectionEnd !== undefined ? targetTextarea.selectionEnd : targetTextarea.value.length;
            const val = targetTextarea.value;
            
            targetTextarea.value = val.substring(0, start) + ' ' + tagsString + ' ' + val.substring(end);
            targetTextarea.focus();
            targetTextarea.selectionStart = targetTextarea.selectionEnd = start + tagsString.length + 2;
            targetTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            targetTextarea.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Attach "@" key listener on all relevant textareas
    document.addEventListener('keyup', function (e) {
        if (e.key === '@' && (e.target.tagName === 'TEXTAREA' || e.target.id === 'answer_editor' || e.target.id === 'full_answer' || e.target.name === 'full_answer')) {
            window.openFatwaMentionPicker(e.target, '', false);
        }
    });
});
</script>
